<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class InitialSetupSeeder extends Seeder
{
    public function run(): void
    {
        // --- 権限 ---
        $permissions = [
            'manage projects',
            'manage requirements',
            'manage test cases',
            'approve changes',
            'run tests',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm, 'guard_name' => 'web']
            );
        }

        // --- ロール ---
        $admin   = Role::firstOrCreate(['name' => 'admin',   'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $tester  = Role::firstOrCreate(['name' => 'tester',  'guard_name' => 'web']);

        // --- 権限付与（冪等） ---
        $admin->syncPermissions($permissions);
        $manager->syncPermissions(['manage projects', 'manage requirements', 'manage test cases', 'approve changes']);
        $tester->syncPermissions(['run tests']);

        // --- 管理者ユーザー ---
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'              => 'Admin',
                'password'          => Hash::make('password'),
                'is_admin'          => 1,                // 独自カラムに合わせる
                'login_code'        => 'hanako',       // 必要なければ外してOK
                'email_verified_at' => now(),            // 必要に応じて
                'note'              => '初期管理者アカウント',
            ]
        );

        // すでに role が付いている場合も重複せず冪等
        if (! $user->hasRole('admin')) {
            $user->assignRole($admin);
        }
    }
}
