<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BulletTestCaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SpecificationController;
use App\Http\Controllers\SpecImageController;
use App\Http\Controllers\SpecMdController;
use App\Http\Controllers\SpecificationMdListController;
use App\Models\Project;
use Illuminate\Support\Facades\Route;

// 共通パラメータ制約（日本語OK／スラッシュのみ禁止）
Route::pattern('set', '[^/]+');

Route::middleware('auth')->group(function () {
    // / にアクセスしたらプロジェクト選択画面へ
    Route::get('/', function () {
        $projects = Project::orderBy('key')->get();
        return view('projects.select', compact('projects'));
    })->name('projects.select');

    // ダッシュボード
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('verified')
        ->name('dashboard');

    // プロフィール
    Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    // 仕様書（DB管理リソース）
    Route::resource('specifications', SpecificationController::class);
    Route::prefix('spec-change-requests')
        ->name('spec-change-requests.')
        ->controller(SpecificationController::class)
        ->group(function () {
            // ← これを追加：詳細表示
            Route::get('{cr}', 'showChangeRequest')->name('show');  // spec-change-requests.show

            // 既存（承認）
            Route::post('{cr}/approve', 'approve')->name('approve');
    });


    // 画像アップロード（spec-images）
    Route::prefix('spec-images')->name('spec-images.')->controller(SpecImageController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/upload', 'store')->name('upload');
    });

    // Markdownファイル（spec-md）
    Route::prefix('spec-md')->name('spec-md.')->controller(SpecMdController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/upload', 'store')->name('upload');
        Route::get('/show/{filename}', 'show')->where('filename', '.*')->name('show');
        Route::get('/download/{filename}', 'download')->where('filename', '.*')->name('download');
    });

    // 仕様書セット（spec-sets）
    Route::prefix('spec-sets')->name('specsets.')->controller(SpecificationMdListController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/upload', 'upload')->name('upload');

        Route::get('{set}', 'showIndex')->name('show');
        Route::get('{set}/view/{path?}', 'show')->where('path', '.*')->name('view');
        Route::get('{set}/file/{path}', 'file')->where('path', '.*')->name('file');
        Route::patch('{set}/rename', 'rename')->name('rename');
    });

    // プロジェクト単位のテストケース
    Route::prefix('projects/{project}')
        ->name('bullet-cases.')
        ->controller(BulletTestCaseController::class)
        ->group(function () {
            Route::get('bullet-cases', 'index')->name('index');
            Route::get('bullet-cases/create', 'create')->name('create');
            Route::post('bullet-cases', 'store')->name('store');
        });

    // テストケース行操作（行単位）
    Route::prefix('bullet-case-rows')->name('bullet-cases.rows.')->controller(BulletTestCaseController::class)->group(function () {
        Route::patch('{row}', 'update')->name('update');
        Route::post('{row}/toggle', 'toggle')->name('toggle');
    });
});

require __DIR__.'/auth.php';
