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

        // 直リンクURLではなく、純粋なファイル名配列を渡す
        $docs = $files;

        return view('spec-md.index', compact('docs'));
    }


    public function show(string $filename)
    {
        $path = "spec-md/{$filename}";
        abort_unless(Storage::disk('public')->exists($path), 404);

        $content = Storage::disk('public')->get($path);

        // 念のためUTF-8に正規化
        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8, SJIS-win, CP932, EUC-JP, ISO-2022-JP');
        }

        // Markdown → HTML（安全寄りのオプション）
        $html = Str::markdown($content, [
            'html_input' => 'strip',          // 生HTMLは除去
            'allow_unsafe_links' => false,
            // 'renderer' => ['soft_break' => "<br />"], // 必要なら改行を<br>に
        ]);

        // 専用ビューで表示（text/html）
        return view('spec-md.show', [
            'filename' => $filename,
            'html' => $html,
        ]);
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

        // 保存先ディレクトリを準備
        Storage::disk('public')->makeDirectory('spec-md');

        // 一意なファイル名生成
        $ext  = $file->getClientOriginalExtension() ?: 'md';
        $name = now()->format('Ymd_His') . '_' . Str::random(8) . '.' . $ext;
        $path = "spec-md/{$name}";

        // 元データを取得
        $raw = file_get_contents($file->getRealPath());

        // UTF-8に正規化（Shift_JIS, CP932, EUC-JP, ISO-2022-JP などから変換）
        if (!mb_check_encoding($raw, 'UTF-8')) {
            $raw = mb_convert_encoding($raw, 'UTF-8', 'UTF-8, SJIS-win, CP932, EUC-JP, ISO-2022-JP');
        }

        // UTF-8で保存
        Storage::disk('public')->put($path, $raw);

        $abs = Storage::disk('public')->path($path);
        $url = route('spec-md.show', ['filename' => $name]); // 表示はコントローラ経由に誘導

        Log::info('spec-md saved', compact('path','abs','url'));

        return back()->with('status', "アップロードOK: {$path}");
    }


    public function download(string $filename)
    {
        $path = "spec-md/{$filename}";
        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->download($path, $filename, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
        ]);
    }

}
