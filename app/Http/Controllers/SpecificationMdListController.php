<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\CommonMark\CommonMarkConverter;
use ZipArchive;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpecificationMdListController extends Controller
{
    private string $root = 'spec-md-sets'; // public ディスク配下

    /** 一覧 */
    public function index()
    {
        $disk = Storage::disk('public');
        $sets = [];

        if ($disk->exists($this->root)) {
            foreach ($disk->directories($this->root) as $dir) {
                $slug = basename($dir);
                $sets[] = [
                    'slug' => $slug,
                    'existsIndex' => $disk->exists("$dir/index.md"),
                ];
            }
        }

        return view('spec-sets.index', compact('sets'));
    }

    /** ZIP アップロード（index.md を含む 1セット） */
    public function upload(Request $request)
    {
        $request->validate([
            'zip' => ['required','file','mimes:zip','max:51200'], // 50MB
            'set_name' => ['nullable','string','max:100'],
        ]);

        $zipFile = $request->file('zip');
        $slug = Str::slug($request->input('set_name') ?: pathinfo($zipFile->getClientOriginalName(), PATHINFO_FILENAME));
        if ($slug === '') $slug = 'set_'.Str::random(6);

        $disk = Storage::disk('public');
        $base = "{$this->root}/{$slug}";
        $disk->makeDirectory($base);

        // 一時保存
        $tmp = $zipFile->getRealPath();
        $za = new ZipArchive();
        if ($za->open($tmp) !== true) {
            return back()->with('status', 'ZIPを開けませんでした。');
        }

        // 展開（ディレクトリトラバーサル対策）
        for ($i=0; $i<$za->numFiles; $i++) {
            $entry = $za->getNameIndex($i);

            // 無効パス拒否
            if (str_contains($entry, '..') || str_starts_with($entry, '/') || str_starts_with($entry, '\\')) {
                continue;
            }

            // 先頭のルートフォルダを剥がす（ZIPにトップフォルダが入っている想定に対応）
            $clean = preg_replace('@^([^/\\\\]+)[/\\\\]@', '', $entry, 1, $removed);
            if ($removed === 0) $clean = $entry; // もともと直下

            // 空エントリや隠しはスキップ
            if ($clean === '' || str_ends_with($clean, '/')) {
                $disk->makeDirectory("$base/$clean");
                continue;
            }

            // 抽出
            $stream = $za->getStream($entry);
            if ($stream === false) continue;
            $content = stream_get_contents($stream);
            fclose($stream);

            // md は UTF-8 正規化
            if (Str::endsWith(Str::lower($clean), '.md')) {
                if (!mb_check_encoding($content, 'UTF-8')) {
                    $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8, SJIS-win, CP932, EUC-JP, ISO-2022-JP');
                }
            }
            $disk->put("$base/$clean", $content);
        }
        $za->close();

        // index.md が無ければエラー
        if (!$disk->exists("$base/index.md")) {
            // 片付け（任意）
            // $disk->deleteDirectory($base);
            return back()->with('status', "index.md が見つかりません（セット: {$slug}）");
        }

        Log::info('spec-set uploaded', ['set' => $slug, 'base' => $base]);
        return back()->with('status', "アップロードOK: {$slug}");
    }

    /** セットの index.md を表示 */
    public function showIndex(string $set)
    {
        return $this->show($set, 'index.md');
    }

    /** md をレンダリング表示（set 内相対 md） */
    public function show(string $set, string $path = 'index.md')
    {
        $disk = Storage::disk('public');
        $baseDir = "{$this->root}/{$set}";

        // パストラバーサル防止
        if (str_contains($path, '..')) abort(404);

        $mdPath = "$baseDir/$path";
        abort_unless($disk->exists($mdPath), 404);

        $content = $disk->get($mdPath);
        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8, SJIS-win, CP932, EUC-JP, ISO-2022-JP');
        }

        // 相対パスを「表示用 or ファイル配信用」に変換
        // 1) 画像など資材 → specsets.file
        // 2) .md へのリンク → specsets.view
        $content = $this->rewriteRelativeLinks($content, $set, dirname($path));

        // Markdown → HTML
        $converter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
        $html = $converter->convert($content)->getContent();

        return view('spec-sets.show', [
            'set' => $set,
            'path' => $path,
            'html' => $html,
        ]);
    }

    /** 資材配信（画像, css, js, pdf など全て） */
    public function file(string $set, string $path)
    {
        if (str_contains($path, '..')) abort(404);

        $disk = Storage::disk('public');
        $full = "{$this->root}/{$set}/{$path}";
        abort_unless($disk->exists($full), 404);

        // content-type を簡易推定（拡張子ベース）
        $mime = $this->guessMime($path);
        $stream = $disk->readStream($full);

        return new StreamedResponse(function () use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $mime,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /** 相対リンクを書き換え */
    private function rewriteRelativeLinks(string $md, string $set, string $currentDir): string
    {
        $normalize = function (string $target) use ($currentDir): string {
            // ./ や ../ を解決（簡易版）
            $base = trim($currentDir, '/');
            $combined = $base ? $base.'/'.$target : $target;
            $parts = [];
            foreach (explode('/', str_replace('\\','/',$combined)) as $seg) {
                if ($seg === '' || $seg === '.') continue;
                if ($seg === '..') { array_pop($parts); continue; }
                $parts[] = $seg;
            }
            return implode('/', $parts);
        };

        // 画像: ![alt](relative/path)
        $md = preg_replace_callback('/!\[([^\]]*)\]\((?!https?:|\/)([^)]+)\)/u', function ($m) use ($set, $normalize) {
            $rel = trim($m[2]);
            $norm = $normalize($rel);
            $url = route('specsets.file', ['set'=>$set, 'path'=>$norm]);
            return '!['.$m[1].']('.$url.')';
        }, $md);

        // リンク: [text](relative.md or relative/path)
        $md = preg_replace_callback('/\[(.*?)\]\((?!https?:|\/)([^)]+)\)/u', function ($m) use ($set, $normalize) {
            $rel = trim($m[2]);
            $norm = $normalize($rel);
            if (Str::endsWith(Str::lower($norm), '.md')) {
                $url = route('specsets.view', ['set'=>$set, 'path'=>$norm]);
            } else {
                $url = route('specsets.file', ['set'=>$set, 'path'=>$norm]);
            }
            return '['.$m[1].']('.$url.')';
        }, $md);

        return $md;
    }

    private function guessMime(string $path): string
    {
        $ext = Str::lower(pathinfo($path, PATHINFO_EXTENSION));
        return match ($ext) {
            'png' => 'image/png',
            'jpg','jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'css' => 'text/css; charset=UTF-8',
            'js'  => 'application/javascript; charset=UTF-8',
            'pdf' => 'application/pdf',
            'md'  => 'text/markdown; charset=UTF-8',
            'txt' => 'text/plain; charset=UTF-8',
            default => 'application/octet-stream',
        };
    }
}
