<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BulletTestCaseGroup extends Model
{
    // ★ title と source_text を fillable に追加（source_text を保存しないなら外してOK）
    protected $fillable = ['project_id', 'title', 'order_no', 'source_text'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function rows(): HasMany
    {
        return $this->hasMany(BulletTestCaseRow::class, 'group_id')->orderBy('order_no');
    }
}
