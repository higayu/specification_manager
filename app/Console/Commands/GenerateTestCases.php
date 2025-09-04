<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Requirement;
use App\Models\TestCase;
use App\Models\TestStep;

class GenerateTestCases extends Command
{
    protected $signature = 'app:generate-test-cases {--overwrite : 既存のテストケースを削除して作り直す}';

    protected $description = '要件(requirements)からテストケースとテスト手順を自動生成する';

    public function handle()
    {
        // オプション: 既存データを削除
        if ($this->option('overwrite')) {
            TestStep::truncate();
            TestCase::truncate();
            $this->info('既存のテストケースとテスト手順を削除しました。');
        }

        $requirements = Requirement::all();
        $caseCount = 0;
        $stepCount = 0;

        foreach ($requirements as $req) {
            // 既にテストケースがあればスキップ
            if (TestCase::where('requirement_id', $req->id)->exists()) {
                continue;
            }

            // ✅ テストケース生成
            $testCase = TestCase::create([
                'requirement_id'  => $req->id,
                'title'           => "【自動生成】{$req->title}",
                'preconditions'   => "要件ID: {$req->id} に基づく前提条件",
                'expected_result' => "要件内容に基づいて仕様通りに動作すること",
                'created_by'      => 1, // 管理者ユーザーID（必要に応じて調整）
            ]);

            $caseCount++;

            // ✅ 基本的なテスト手順を自動生成
            $steps = [
                ['step_no' => 1, 'action' => "システムを起動する", 'expected_result' => "正常に起動する"],
                ['step_no' => 2, 'action' => "対象機能を実行する", 'expected_result' => "要件に記載された通りに動作する"],
                ['step_no' => 3, 'action' => "異常値を入力する", 'expected_result' => "エラーメッセージが表示される"],
            ];

            foreach ($steps as $s) {
                TestStep::create([
                    'test_case_id'   => $testCase->id,
                    'step_no'        => $s['step_no'],
                    'action'         => $s['action'],
                    'expected_result'=> $s['expected_result'],
                ]);
                $stepCount++;
            }
        }

        $this->info("{$caseCount} 件のテストケースと {$stepCount} 件のテスト手順を生成しました。");
    }
}
