<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Requirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'current_version_id',
    ];

    // プロジェクト（もし Project モデルがあるなら）
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // バージョン履歴
    public function versions()
    {
        return $this->hasMany(SpecVersion::class);
    }

    // 現行バージョン
    public function currentVersion()
    {
        return $this->belongsTo(SpecVersion::class, 'current_version_id');
    }

    // 変更要求
    public function changeRequests()
    {
        return $this->hasMany(ChangeRequest::class);
    }
}
