<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecificationVersion extends Model
{
    // テーブル名が "specification_versions" なら指定不要
    // もしスキーマが "spec_versions" なら ↓ を有効化:
    // protected $table = 'spec_versions';

    protected $fillable = [
        'specification_id',
        'version_no',
        'body_md',
        'attributes',
        'created_by',
    ];

    protected $casts = [
        'attributes' => 'array',
    ];

    public function specification(): BelongsTo
    {
        return $this->belongsTo(Specification::class);
    }

    public function createdBy(): BelongsTo
    {
        // users.id への外部キーが created_by の前提
        return $this->belongsTo(User::class, 'created_by');
    }
}
