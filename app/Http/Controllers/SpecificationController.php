<?php

namespace App\Http\Controllers;

use App\Models\{Project, Specification, SpecificationVersion, SpecChangeRequest};
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class SpecificationController extends Controller
{
    /** 一覧（?project=ID 必須） */
    public function index(Request $request): View
    {
        $projectId = (int) $request->input('project');
        $project   = Project::findOrFail($projectId);

        // Project に specifications() が未定義でも動くようにクエリ直指定
        $specs = Specification::where('project_id', $project->id)
            ->with('currentVersion')
            ->orderBy('code')
            ->paginate(20);

        return view('specifications.index', compact('project', 'specs'));
    }

    /** 作成フォーム（?project=ID 必須） */
    public function create(Request $request): View
    {
        $projectId = (int) $request->input('project');
        $project   = Project::findOrFail($projectId);

        return view('specifications.create', compact('project'));
    }



    public function store(Request $req): RedirectResponse
    {
        $data = $req->validate([
            'project_id'    => ['required','exists:projects,id'],
            'code'          => ['required','string','max:255'],
            'title'         => ['required','string','max:255'],
            'body_md'       => ['required','string'],
            'attributes'    => ['nullable','array'],
            'images'        => ['nullable','array'],
            'images.*'      => ['file','image','mimes:jpg,jpeg,png,gif,webp','max:5120'],
            'append_images' => ['nullable'],
        ]);

        DB::beginTransaction();
        $storedPaths = [];

        try {
            $appendMd = '';
            if ($req->hasFile('images')) {
                $lines = [];
                foreach ($req->file('images') as $file) {
                    if (!$file->isValid()) continue;
                    $path = $file->store('spec-images/' . date('Y/m'), 'public');
                    if ($path) {
                        $storedPaths[] = $path;
                        $url  = Storage::url($path);
                        $alt  = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $lines[] = "![{$alt}]({$url})";
                    }
                }
                if (!empty($lines) && $req->boolean('append_images')) {
                    $appendMd = "\n\n" . implode("\n\n", $lines) . "\n";
                }
            }

            // 仕様ヘッダ
            $spec = Specification::create([
                'project_id' => $data['project_id'],
                'code'       => $data['code'],
                'title'      => $data['title'],
                'status'     => 'approved', // 運用ポリシーに合わせて変更可
            ]);

            // v1
            $v1 = $spec->versions()->create([
                'version_no' => 1,
                'body_md'    => ($data['body_md'] ?? '') . $appendMd,
                'attributes' => $data['attributes'] ?? [],
                'created_by' => auth()->id(),
            ]);

            $spec->update(['current_version_id' => $v1->id]);

            DB::commit();

            return redirect()->route('specifications.show', [
                'specification' => $spec->id,
                'project'       => $data['project_id'],
            ]);
        } catch (\Throwable $e) {
            foreach ($storedPaths as $p) {
                Storage::disk('public')->delete($p);
            }
            DB::rollBack();
            throw $e;
        }
    }



    /** 詳細表示（?project=ID があればそれを優先） */
    public function show(Request $request, Specification $specification): \Illuminate\View\View
    {
        // ① プロジェクト解決
        $project = Project::findOrFail(
            (int) $request->input('project', $specification->project_id)
        );

        // ② 現在版を“安全に”取得（無ければ最新版を採用）
        //    ※ 関係名は currentVersion（キャメルケース）
        $specification->loadMissing(['currentVersion']);

        $ver = $specification->currentVersion;

        if (!$ver) {
            // current_version_id が未設定 or 指すレコードが無い場合は最新を拾う
            $ver = $specification->versions()
                ->orderByDesc('version_no')
                ->orderByDesc('id')
                ->first();
        }

        // ③ ビューへ
        return view('specifications.show', [
            'project'       => $project,
            'specification' => $specification,
            'ver'           => $ver,            // ← ビューはこれを見る
        ]);
    }


    /** 変更申請を伴う更新（新バージョン作成＋CR作成） */
    public function update(Request $req, Specification $specification): RedirectResponse
    {
        if (!$specification->exists || !$specification->getKey()) {
            abort(404, 'Specification not found');
        }

        $data = $req->validate([
            'title'         => ['required','string','max:255'],
            'body_md'       => ['required','string'],
            'attributes'    => ['nullable','array'],
            'reason'        => ['required','string'],
            'impact'        => ['nullable','string'],
            'images'        => ['nullable','array'],
            'images.*'      => ['file','image','mimes:jpg,jpeg,png,gif,webp','max:5120'],
            'append_images' => ['nullable'],
        ]);

        DB::beginTransaction();
        $storedPaths = []; // 失敗時に掃除するため

        try {
            // 1) 画像 → Markdown 追記文字列を構築
            $appendMd = '';
            if ($req->hasFile('images')) {
                $lines = [];
                foreach ($req->file('images') as $file) {
                    if (!$file->isValid()) continue;
                    $path = $file->store('spec-images/' . date('Y/m'), 'public');
                    if ($path) {
                        $storedPaths[] = $path;
                        $url  = Storage::url($path);
                        $alt  = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $lines[] = "![{$alt}]({$url})";
                    }
                }
                if (!empty($lines) && $req->boolean('append_images')) {
                    $appendMd = "\n\n" . implode("\n\n", $lines) . "\n";
                }
            }

            // 2) 次の版番号を一度だけ決定
            $nextNo = (int) ($specification->versions()->max('version_no') ?? 0) + 1;

            // 3) 新バージョン作成（★ リレーション経由、ここで1回だけ）
            $next = $specification->versions()->create([
                'version_no' => $nextNo,
                'body_md'    => ($data['body_md'] ?? '') . $appendMd,
                'attributes' => $data['attributes'] ?? [],
                'created_by' => auth()->id(),
            ]);

            // 4) 変更要求（CR）作成
            $curr = $specification->currentVersion; // null でもOK
            $cr = SpecChangeRequest::create([
                'project_id'       => $specification->project_id,
                'specification_id' => $specification->id,
                'from_version_id'  => optional($curr)->id,
                'to_version_id'    => $next->id,
                'reason'           => $data['reason'],
                'impact'           => $data['impact'] ?? null,
                'status'           => 'proposed',
                'requested_by'     => auth()->id(),
            ]);

            // 5) タイトル反映（必要に応じて）
            $specification->update(['title' => $data['title']]);

            DB::commit();

            return redirect()->route('spec-change-requests.show', [
                'cr'      => $cr->id,
                'project' => $specification->project_id,
            ]);
        } catch (\Throwable $e) {
            // 失敗時にアップロード済ファイルを削除
            foreach ($storedPaths as $p) {
                Storage::disk('public')->delete($p);
            }
            DB::rollBack();
            throw $e;
        }
    }

    /** CR 承認（現行版を切替） */
    public function approve(\Illuminate\Http\Request $request, SpecChangeRequest $cr): \Illuminate\Http\RedirectResponse
    {
        // 既に承認済ならそのまま仕様詳細へ
        if ($cr->status === 'approved') {
            return redirect()->route('specifications.show', [
                'specification' => $cr->specification_id,
                'project'       => $cr->project_id,
            ])->with('status', 'この変更要求は既に承認済みです。');
        }

        DB::transaction(function () use ($cr) {
            // 防御：to_version_id が無ければ承認不可
            if (!$cr->to_version_id) {
                throw new \RuntimeException('to_version_id が存在しません。');
            }

            // 仕様の現行版を提案版に切替
            $cr->specification()->update([
                'current_version_id' => $cr->to_version_id,
            ]);

            // CR を承認済みに
            $cr->forceFill([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
            ])->save();
        });

        // 承認後は仕様詳細へ（編集が反映された状態）
        return redirect()->route('specifications.show', [
            'specification' => $cr->specification_id,
            'project'       => $cr->project_id,
        ])->with('status', '変更を反映しました。');
    }


    public function edit(Request $request, Specification $specification): View
    {
        // ?project=xx が来ていればそれを優先
        $project = Project::findOrFail(
            (int) $request->input('project', $specification->project_id)
        );

        // 現在版を読み込み（無ければ最新版を代替）
        $specification->loadMissing(['currentVersion']);
        $ver = $specification->currentVersion;
        if (!$ver) {
            $ver = $specification->versions()->orderByDesc('version_no')->orderByDesc('id')->first();
        }

        return view('specifications.edit', [
            'project'       => $project,
            'specification' => $specification,
            'ver'           => $ver,
        ]);
    }


    public function showChangeRequest(Request $request, SpecChangeRequest $cr): View
    {
        // ?project= が来ていれば優先、無ければ CR 側
        $project = Project::findOrFail(
            (int) $request->input('project', $cr->project_id)
        );

        // 必要なら関連を lazy load（リレーション名は実装に合わせて）
        $cr->loadMissing([
            'specification',
            'fromVersion',
            'toVersion',
            'requestedBy',
            // 'approvedBy',
        ]);

        return view('spec_change_requests.show', [
            'project' => $project,
            'cr'      => $cr,
        ]);
    }


}
