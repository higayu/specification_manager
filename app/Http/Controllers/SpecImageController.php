<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SpecImageController extends Controller
{
    public function index()
    {
        $dir = storage_path('app/public/spec-images');
        $files = is_dir($dir) ? array_values(array_filter(scandir($dir), fn($f) =>
            !in_array($f, ['.','..']) && is_file("{$dir}/{$f}")
        )) : [];

        $images = array_map(fn($f) => asset("storage/spec-images/{$f}"), $files);

        return view('spec-images.index', compact('images'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => ['required','image','mimes:jpeg,jpg,png,webp','max:5120'], // 5MB
        ]);

        $file = $validated['image']; // UploadedFile
        if (!$file) {
            return back()->with('status', 'ファイルが取得できませんでした。');
        }

        // 念のためディレクトリ作成（存在してもOK）
        Storage::disk('public')->makeDirectory('spec-images');

        // 一意なファイル名
        $ext  = $file->getClientOriginalExtension();
        $name = now()->format('Ymd_His') . '_' . Str::random(8) . '.' . $ext;

        // ★ ディスクを明示して保存（失敗なら例外が飛ぶ）
        $savedPath = Storage::disk('public')->putFileAs('spec-images', $file, $name);

        // 物理パスと公開URLをログ＆画面で確認
        $abs = Storage::disk('public')->path($savedPath);           // storage/app/public/spec-images/xxx
        $url = asset('storage/spec-images/'.$name);                 // public/storage/spec-images/xxx

        Log::info('spec-image saved', ['savedPath' => $savedPath, 'abs' => $abs, 'url' => $url]);

        return back()->with('status', "アップロードOK: {$savedPath}");
    }
}
