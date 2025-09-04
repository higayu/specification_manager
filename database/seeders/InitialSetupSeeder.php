<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Requirement;
use App\Models\SpecVersion;
use App\Models\TestCase;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class InitialSetupSeeder extends Seeder
{
    public function run()
    {
        // --- 権限の作成 ---
        $permissions = [
            'manage projects',
            'manage requirements',
            'manage test cases',
            'approve changes',
            'run tests',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm, 'guard_name' => 'web']   // ← guard_name を明示
            );
        }

        // --- ロールの作成 ---
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $tester = Role::firstOrCreate(['name' => 'tester', 'guard_name' => 'web']);

        // --- 権限の紐づけ ---
        $admin->syncPermissions($permissions);
        $manager->syncPermissions(['manage projects', 'manage requirements', 'manage test cases', 'approve changes']);
        $tester->syncPermissions(['run tests']);

        // --- 管理者ユーザー作成 ---
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        $user->assignRole('admin');   // ← これでOKになる
    }
}

