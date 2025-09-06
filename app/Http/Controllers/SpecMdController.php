<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\StoragePermissionService;

class SpecMdController extends Controller
{
    public function index()
    {
        $dir = storage_path('app/public/spec-md');
        $files = is_dir($dir) ? array_values(array_filter(scandir($dir), fn($f) =>
            !in_array($f, ['.','..']) && is_file("{$dir}/{$f}")
        )) : [];

        $docs = array_map(fn($f) => asset("storage/spec-md/{$f}"), $files);

        return view('spec-md.index', compact('docs'));
    }

    public function store(Request $request, StoragePermissionService $permService)
    {
        // ファイルアップロード前に権限チェック
        $check = $permService->check('spec-md');
        if (!$check['writable']) {
            return back()->with('status', '保存先ディスクに書き込みできません。管理者に連絡してください。');
        }

        Log::info('upload debug', [
            'hasFile'     => $request->hasFile('mdfile'),
            'isValid'     => $request->file('mdfile')?->isValid(),
            'origName'    => $request->file('mdfile')?->getClientOriginalName(),
            'clientMime'  => $request->file('mdfile')?->getClientMimeType(),
        ]);

        $validated = $request->validate([
            'mdfile' => ['required','file','mimetypes:text/markdown,text/plain','max:10240'],
        ]);

        $file = $validated['mdfile'];
        if (!$file) {
            return back()->with('status', 'ファイルが取得できませんでした。');
        }

        Storage::disk('public')->makeDirectory('spec-md');

        $ext  = $file->getClientOriginalExtension();
        $name = now()->format('Ymd_His') . '_' . Str::random(8) . '.' . $ext;

        $savedPath = Storage::disk('public')->putFileAs('spec-md', $file, $name);

        $abs = Storage::disk('public')->path($savedPath);
        $url = asset('storage/spec-md/'.$name);

        Log::info('spec-md saved', compact('savedPath','abs','url'));

        return back()->with('status', "アップロードOK: {$savedPath}");
    }
}
