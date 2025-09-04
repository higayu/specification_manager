<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SpecVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'requirement_id',
        'version',
        'description',
        'created_by',
    ];

    // 紐付く要件
    public function requirement()
    {
        return $this->belongsTo(Requirement::class);
    }

    // 作成者（Userモデル）
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // このバージョンを参照している変更要求（旧側）
    public function oldChangeRequests()
    {
        return $this->hasMany(ChangeRequest::class, 'old_version_id');
    }

    // このバージョンを参照している変更要求（新側）
    public function newChangeRequests()
    {
        return $this->hasMany(ChangeRequest::class, 'new_version_id');
    }
}
