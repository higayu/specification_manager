<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BulletTestCaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SpecificationController;
use App\Http\Controllers\SpecImageController;   // ← これを追加！
use App\Http\Controllers\SpecMdController;
use App\Models\Project; // ← プロジェクトモデルを使う
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // / にアクセスしたらプロジェクト選択画面へ
    Route::get('/', function () {
        $projects = Project::orderBy('key')->get();
        return view('projects.select', compact('projects'));
    })->name('projects.select');

    Route::post('/spec-images/upload', [SpecImageController::class, 'store'])->name('spec-images.upload');
    Route::get('/spec-images', [SpecImageController::class, 'index'])->name('spec-images.index');

    Route::get('/spec-md', [SpecMdController::class, 'index'])->name('spec-md.index');
    Route::post('/spec-md/upload', [SpecMdController::class, 'store'])->name('spec-md.upload');

    // 追加：表示/ダウンロード
    Route::get('/spec-md/show/{filename}', [SpecMdController::class, 'show'])
        ->where('filename', '.*')->name('spec-md.show');

    Route::get('/spec-md/download/{filename}', [SpecMdController::class, 'download'])
        ->where('filename', '.*')->name('spec-md.download');

    // routes/web.php
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['verified'])
        ->name('dashboard');

    Route::resource('specifications', SpecificationController::class)->middleware(['auth']);
    Route::post('spec-change-requests/{cr}/approve', [SpecificationController::class,'approve'])
    ->name('spec-change-requests.approve')->middleware(['auth']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // プロジェクト単位のテストケース関連
    Route::prefix('projects/{project}')->group(function () {
        Route::get('bullet-cases', [BulletTestCaseController::class,'index'])->name('bullet-cases.index');
        Route::get('bullet-cases/create', [BulletTestCaseController::class,'create'])->name('bullet-cases.create');
        Route::post('bullet-cases', [BulletTestCaseController::class,'store'])->name('bullet-cases.store');
    });

    Route::patch('bullet-case-rows/{row}', [BulletTestCaseController::class,'update'])
        ->name('bullet-cases.rows.update');
    // 行の完了フラグ切替
    Route::post('bullet-case-rows/{row}/toggle', [BulletTestCaseController::class,'toggle'])->name('bullet-cases.rows.toggle');
});

require __DIR__.'/auth.php';
