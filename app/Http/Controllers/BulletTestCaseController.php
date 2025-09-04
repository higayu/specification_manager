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

    /** テキスト取り込み */
    public function store(
        Request $request,
        Project $project,
        BulletTestCaseImportService $svc
    ): RedirectResponse {
        $data = $request->validate([
            'text'  => ['required', 'string'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $svc->import($project, $data['text'], $data['title'] ?? null);

        return redirect()
            ->route('bullet-cases.index', ['project' => $project])
            ->with('status', 'インポートしました');
    }

    /** 行の完了フラグ切替 */
    public function toggle(BulletTestCaseRow $row): RedirectResponse
    {
        $row->is_done = ! $row->is_done;
        $row->save();

        return back()->with('status', '状態を切り替えました');
    }
}
