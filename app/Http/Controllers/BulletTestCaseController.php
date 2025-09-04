<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\BulletTestCaseGroup;
use App\Models\BulletTestCaseRow;
use App\Services\BulletTestCaseImportService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BulletTestCaseController extends Controller
{
    /** 一覧（グループごとにテーブル表示） */
    public function index(Project $project): View
    {
        $groups = BulletTestCaseGroup::with([
                'rows' => fn ($q) => $q->orderBy('order_no')->orderBy('id'),
            ])
            ->where('project_id', $project->id)
            ->orderBy('order_no')
            ->get();

        return view('bullet_cases.index', compact('project', 'groups'));
    }

    /** インポート画面 */
    public function create(Project $project): View
    {
        return view('bullet_cases.create', compact('project'));
    }


    public function store(Request $request, Project $project, BulletTestCaseImportService $svc)
    {
        // app/Http/Controllers/BulletTestCaseController.php の store()

        $data = $request->validate([
            'mode'  => ['required', 'in:single,bulk'],
            'text'  => ['required', 'string'],
            'title' => ['exclude_if:mode,bulk', 'nullable', 'string', 'max:255'],
        ]);

        $svc->import(
            $project,
            $data['text'],
            $data['mode'] === 'single' ? ($data['title'] ?? null) : null
        );

        $mode = $data['mode'] ?? 'single';

        if ($mode === 'bulk') {
            [$groups, $rowsTotal] = $svc->importMany($project, $data['text']);
            return redirect()
                ->route('bullet-cases.index', $project)
                ->with('ok', "一括実行：{$groups}グループ／合計{$rowsTotal}行を取り込みました。");
        } else {
            $g = $svc->import($project, $data['text']);
            return redirect()
                ->route('bullet-cases.index', $project)
                ->with('ok', "１つずつ：『{$g->title}』を取り込みました（行数：{$g->rows()->count()}）。");
        }
    }


    /** 行の完了フラグ切替 */
    public function toggle(BulletTestCaseRow $row): RedirectResponse
    {
        $row->is_done = ! $row->is_done;
        $row->save();

        return back()->with('status', '状態を切り替えました');
    }
}
