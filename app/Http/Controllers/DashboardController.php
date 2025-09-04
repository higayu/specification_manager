<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
// app/Http/Controllers/DashboardController.php
use App\Models\{Project, BulletTestCaseGroup, BulletTestCaseRow};

class DashboardController extends Controller
{

    /** ダッシュボード表示 */
    public function index(): View
    {
        // サマリー
        $projectsCount   = Project::count();
        $groupsCount     = BulletTestCaseGroup::count();
        $rowsCount       = BulletTestCaseRow::count();
        $rowsDoneCount   = BulletTestCaseRow::where('is_done', true)->count();

        // 最近作ったグループ（直近5件）
        $recentGroups = BulletTestCaseGroup::with('project')
            ->withCount([
                'rows',
                'rows as rows_done_count' => fn($q) => $q->where('is_done', true),
            ])
            ->latest()  // created_at desc
            ->take(5)
            ->get();

        // クイックリンク用に全プロジェクト
        $projects = Project::orderBy('id')->get();

        return view('dashboard', compact(
            'projectsCount', 'groupsCount', 'rowsCount', 'rowsDoneCount',
            'recentGroups', 'projects'
        ));
    }
}
