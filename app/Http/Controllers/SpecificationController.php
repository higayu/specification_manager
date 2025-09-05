<?php

namespace App\Http\Controllers;

use App\Models\{Project, Specification, SpecificationVersion, SpecChangeRequest};
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

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



    public function store(Request $req): \Illuminate\Http\RedirectResponse
    {
        $data = $req->validate([
            'project_id' => ['required','exists:projects,id'],
            'code'       => ['required','string','max:255'],
            'title'      => ['required','string','max:255'],
            'body_md'    => ['required','string'],
            'attributes' => ['nullable','array'],
            // 画像バリデーション（5MB/枚・形式制限は適宜調整）
            'images'     => ['nullable','array'],
            'images.*'   => ['file','image','mimes:jpg,jpeg,png,gif,webp','max:5120'],
            'append_images' => ['nullable'], // チェックボックス
        ]);


        // 画像を保存し、Markdown を組み立て
        $appendMd = '';
        if ($req->hasFile('images')) {
            $lines = [];
            foreach ($req->file('images') as $file) {
                if (!$file->isValid()) continue;

                // ✅ ディスクは 'public' を指定。パスには 'public/' を付けない
                $subdir = 'spec-images/' . date('Y/m');
                $path   = $file->store($subdir, 'public');   // storage/app/public/spec-images/...
                $url    = Storage::url($path);               // /storage/spec-images/...

                $alt    = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $lines[] = "![{$alt}]({$url})";
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
            'status'     => 'approved',
        ]);

        // v1 作成（本文末尾に画像の Markdown を追加）
        $v1 = SpecificationVersion::create([
            'specification_id' => $spec->id,
            'version_no'       => 1,
            'body_md'          => ($data['body_md'] ?? '') . $appendMd,
            'attributes'       => $data['attributes'] ?? [],
            'created_by'       => auth()->id(),
        ]);

        $spec->update(['current_version_id' => $v1->id]);

        return redirect()->route('specifications.show', [
            'specification' => $spec->id,
            'project'       => $data['project_id'],
        ]);
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
    public function update(Request $req, Specification $spec): RedirectResponse
    {
        // 1) バリデーション（画像もここで定義）
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

        // 2) 画像を保存 → Markdown を末尾に追記するテキストを作る
        $appendMd = '';
        if ($req->hasFile('images')) {
            $lines = [];
            foreach ($req->file('images') as $file) {
                if (!$file->isValid()) continue;
                $path = $file->store('spec-images/' . date('Y/m'), 'public'); // storage/app/public/...
                $url  = Storage::url($path);                                   // /storage/...
                $alt  = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $lines[] = "![{$alt}]({$url})";
            }
            if (!empty($lines) && $req->boolean('append_images')) {
                $appendMd = "\n\n" . implode("\n\n", $lines) . "\n";
            }
        }

        // 3) 現在版と次版番号
        $curr   = $spec->currentVersion; // null の可能性あり
        $nextNo = (int) ($spec->versions()->max('version_no') ?? 0) + 1;

        // 4) 新バージョンを作成（本文＋画像追記）
        $next = SpecificationVersion::create([
            'specification_id' => $spec->id,
            'version_no'       => $nextNo,
            'body_md'          => ($data['body_md'] ?? '') . $appendMd,
            'attributes'       => $data['attributes'] ?? [],
            'created_by'       => auth()->id(),
        ]);

        // 5) 変更要求（CR）を作成（承認後に current_version_id を切替）
        $cr = SpecChangeRequest::create([
            'project_id'       => $spec->project_id,
            'specification_id' => $spec->id,
            'from_version_id'  => optional($curr)->id,
            'to_version_id'    => $next->id,
            'reason'           => $data['reason'],
            'impact'           => $data['impact'] ?? null,
            'status'           => 'proposed',
            'requested_by'     => auth()->id(),
        ]);

        // タイトルも仕様ヘッダに反映（必要なら）
        $spec->update(['title' => $data['title']]);

        // 6) CR 詳細へ
        return redirect()->route('spec-change-requests.show', [
            'cr'      => $cr->id,
            'project' => $spec->project_id,
        ]);
    }


    /** CR 承認（現行版を切替） */
    public function approve(SpecChangeRequest $cr): RedirectResponse
    {
        $cr->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
        ]);

        $cr->specification->update([
            'current_version_id' => $cr->to_version_id,
        ]);

        return back();
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

}
