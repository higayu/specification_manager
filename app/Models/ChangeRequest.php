<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChangeRequest extends Model
{
    use HasFactory;

    // 変更可能なカラムを明示
    protected $fillable = [
        'requirement_id',
        'old_version_id',
        'new_version_id',
        'status',
        'approved_by',
        'approved_at',
    ];

    /**
     * リレーション
     */

    // 要件との関連
    public function requirement()
    {
        return $this->belongsTo(Requirement::class);
    }

    // 古いバージョン
    public function oldVersion()
    {
        return $this->belongsTo(SpecVersion::class, 'old_version_id');
    }

    // 新しいバージョン
    public function newVersion()
    {
        return $this->belongsTo(SpecVersion::class, 'new_version_id');
    }

    // 承認者
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
