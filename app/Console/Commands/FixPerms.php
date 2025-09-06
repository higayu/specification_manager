<?php
// app/Console/Commands/FixPerms.php
namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixPerms extends Command
{
    protected $signature = 'app:fix-perms';
    protected $description = 'Fix storage/bootstrap/cache permissions';

    public function handle(): int
    {
        $paths = [
            storage_path('logs'),
            storage_path('framework/cache'),
            storage_path('framework/data'),
            storage_path('framework/sessions'),
            storage_path('framework/testing'),
            storage_path('framework/views'),
            base_path('bootstrap/cache'),
        ];
        foreach ($paths as $p) if (!is_dir($p)) @mkdir($p, 02775, true);

        // chmod は可能な範囲で（所有権はroot権限がないと変えられません）
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(base_path('storage'), \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($it as $item) {
            @chmod($item->getPathname(), $item->isDir() ? 02775 : 0664);
        }
        $it2 = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(base_path('bootstrap/cache'), \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($it2 as $item) {
            @chmod($item->getPathname(), $item->isDir() ? 02775 : 0664);
        }

        @touch(storage_path('logs/laravel.log'));
        @chmod(storage_path('logs/laravel.log'), 0664);

        $this->info('storage/bootstrap/cache の権限を可能な範囲で修復しました。');
        return self::SUCCESS;
    }
}
