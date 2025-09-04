<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // サンプルユーザー
        $user = User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'), // ログイン用にパスワードも設定
        ]);

        // 初期データ（ロール、権限、サンプルプロジェクトなど）
        $this->call(InitialSetupSeeder::class);

        // 作成したユーザーを admin ロールに割り当てる
        $user->assignRole('admin');
    }
}
