<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulletTestCaseRow extends Model
{
    // テーブル名は規約通りなので指定不要（= bullet_test_case_rows）
    // protected $table = 'bullet_test_case_rows';

    protected $fillable = [
        'group_id',         // FK
        'order_no',         // 並び順（デフォルト1）
        'no',               // 仕様上の番号など
        'feature',          // 対象機能
        'input_condition',  // 入力・前提条件
        'expected',         // 期待結果
        'is_done',          // 完了フラグ
    ];

    protected $casts = [
        'group_id' => 'integer',
        'order_no' => 'integer',
        'is_done'  => 'boolean',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(BulletTestCaseGroup::class, 'group_id');
    }

    // 並び順の共通スコープ（任意）
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_no')->orderBy('id');
    }
}
