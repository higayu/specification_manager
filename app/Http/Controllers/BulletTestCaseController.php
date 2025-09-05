<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\BulletTestCaseGroup;
use App\Models\BulletTestCaseRow;
use App\Services\BulletTestCaseImportService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

class BulletTestCaseController extends Controller
{


    public function index(Request $request, Project $project): View
    {
        $hideDone = $request->boolean('hide_done');         // 完了を非表示
        $priority = $request->integer('priority');          // 1,2,3 or null
        $q        = trim((string)$request->input('q', '')); // キーワード

        // 子行の絞り込み条件（再利用するためクロージャで定義）
        $rowFilter = function ($query) use ($hideDone, $priority, $q) {
            if ($hideDone) {
                $query->where('is_done', 0);
            }
            if (in_array($priority, [1, 2, 3], true)) {
                $query->where('priority', $priority);
            }
            if ($q !== '') {
                $words = preg_split('/\s+/u', $q, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($words as $word) {
                    $like = '%' . $word . '%';
                    $query->where(function ($qq) use ($like) {
                        $qq->where('no', 'like', $like)
                        ->orWhere('feature', 'like', $like)
                        ->orWhere('input_condition', 'like', $like)
                        ->orWhere('expected', 'like', $like)
                        ->orWhere('memo', 'like', $like);
                    });
                }
            }
        };

        // 子行の並び順 + 絞り込み
        $rowsWith = function ($q) use ($rowFilter) {
            $rowFilter($q);
            $q->orderBy('order_no')->orderBy('id');
        };

        $groupsQuery = BulletTestCaseGroup::where('project_id', $project->id)
            ->orderBy('order_no');

        // 🔸 フィルタ指定がある場合のみ親にも whereHas を適用（該当行を持つグループだけ）
        $hasFilter = $hideDone || in_array($priority, [1,2,3], true) || $q !== '';
        if ($hasFilter) {
            $groupsQuery->whereHas('rows', $rowFilter);
        }

        // 子行は常に条件付きで eager load
        $groups = $groupsQuery->with(['rows' => $rowsWith])->get();

        return view('bullet_cases.index', [
            'project' => $project,
            'groups'  => $groups,
            'filters' => [
                'hide_done' => $hideDone,
                'priority'  => in_array($priority, [1,2,3], true) ? (string)$priority : '',
                'q'         => $q,
            ],
        ]);
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

    public function update(Request $request, BulletTestCaseRow $row)
    {
        $data = $request->validate([
            'memo' => 'nullable|string|max:255',
            'priority' => 'required|integer|min:1|max:3',
        ]);

        $row->update($data);

        return back()->with('status', '更新しました');
    }

}
