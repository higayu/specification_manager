<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class StoragePermissionService
{
    /**
     * public ディスクの書き込み/削除権限を確認
     *
     * @param string $directory
     * @return array
     */
    public function check(string $directory = 'spec-md'): array
    {
        $disk = Storage::disk('public');
        $path = $directory.'/_perm_check.txt';

        // 書き込み確認
        $writable = $disk->put($path, 'ok');

        // 削除確認
        $deletable = false;
        if ($writable) {
            $deletable = $disk->delete($path);
        }

        $result = [
            'disk' => 'public',
            'dir' => $directory,
            'writable' => (bool) $writable,
            'deletable' => (bool) $deletable,
            'path' => $disk->path($path),
        ];

        Log::info('Storage permission check', $result);

        return $result;
    }
}
