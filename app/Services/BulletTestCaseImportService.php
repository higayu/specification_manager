<?php
namespace App\Services;

use App\Models\{BulletTestCaseGroup, BulletTestCaseRow, Project};
use Illuminate\Support\Str;

class BulletTestCaseImportService
{
    // ★ 第3引数でフォームの title を受け取れるように
    public function import(Project $project, string $text, ?string $inputTitle = null): BulletTestCaseGroup
    {
        // 改行など正規化（"rn" 混入対策）
        $text = preg_replace("/\r\n|\r|\n|rn/", "\n", $text);

        // 1) フォーム入力 > 2) 先頭見出し(# ...) > 3) 既定値
        $title = trim((string) $inputTitle);
        if ($title === '') {
            if (preg_match('/^\s*#\s*(.+)$/m', $text, $m)) {
                $title = Str::limit(trim($m[1]), 255, '');
            } else {
                $title = 'テストケース';
            }
        }

        $orderBase = (int) (BulletTestCaseGroup::where('project_id', $project->id)->max('order_no') ?? 0);

        $group = BulletTestCaseGroup::create([
            'project_id'  => $project->id,
            'order_no'    => $orderBase + 1,
            'title'       => $title,      // ★ここが fillable で受け入れられる必要あり
            'source_text' => $text,       // 保存しないならこの行は削除
        ]);

        $lines = preg_split('/\n/u', $text);
        $rowNo = 0;

        foreach ($lines as $l) {
            $l = trim($l);
            if (!Str::startsWith($l, '-')) continue;

            // "- a | b | c | d" を分割
            $body  = ltrim($l, "- \t");
            $parts = array_map('trim', explode('|', $body));
            $parts = array_pad($parts, 4, null);
            [$no, $feature, $cond, $expected] = $parts;

            $rowNo++;
            BulletTestCaseRow::create([
                'group_id'        => $group->id,
                'order_no'        => $rowNo,
                'no'              => $no ?: null,
                'feature'         => $feature ?: '',
                'input_condition' => $cond ?: null,
                'expected'        => $expected ?: '',
                'is_done'         => false,
            ]);
        }

        return $group;
    }
}
