<?php

namespace App\Http\Controllers;

use App\Models\{Project, Specification, SpecificationVersion, SpecChangeRequest};
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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

    /** 登録処理 */
    public function store(Request $req): RedirectResponse
    {
        $data = $req->validate([
            'project_id' => ['required','exists:projects,id'],
            'code'       => ['required','string','max:255'],
            'title'      => ['required','string','max:255'],
            'body_md'    => ['required','string'],
            'attributes' => ['nullable','array'], // version 側に持たせる想定
        ]);

        // 仕様ヘッダ
        $spec = Specification::create([
            'project_id' => $data['project_id'],
            'code'       => $data['code'],
            'title'      => $data['title'],
            'status'     => 'approved', // 初期状態の方針に合わせて調整可
        ]);

        // v1 を作成
        $v1 = SpecificationVersion::create([
            'specification_id' => $spec->id,
            'version_no'       => 1,
            'body_md'          => $data['body_md'],
            'attributes'       => $data['attributes'] ?? [],
            'created_by'       => auth()->id(),
        ]);

        // 現在版を v1 に
        $spec->update(['current_version_id' => $v1->id]);

        // project クエリを付けて show へ
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
        $data = $req->validate([
            'title'      => ['required','string','max:255'],
            'body_md'    => ['required','string'],
            'attributes' => ['nullable','array'],
            'reason'     => ['required','string'],
            'impact'     => ['nullable','string'],
        ]);

        $curr   = $spec->currentVersion; // 現行版
        $nextNo = (int) ($spec->versions()->max('version_no') ?? 0) + 1;

        // 次版作成
        $next = SpecificationVersion::create([
            'specification_id' => $spec->id,
            'version_no'       => $nextNo,
            'body_md'          => $data['body_md'],
            'attributes'       => $data['attributes'] ?? [],
            'created_by'       => auth()->id(),
        ]);

        // 変更要求（CR）を作成（承認後に current_version_id を切替）
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
}
