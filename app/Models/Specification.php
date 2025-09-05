<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specification extends Model
{
    protected $fillable = [
        'project_id','code','title','status','current_version_id'
    ];

    protected $casts = [
        'current_version_id' => 'integer',
        'project_id' => 'integer',
    ];

    public function project(){
        return $this->belongsTo(Project::class);
    }

    // 外部キー/ローカルキーを明示
    public function versions(){
        return $this->hasMany(SpecificationVersion::class, 'specification_id', 'id');
    }

    public function currentVersion(){
        return $this->belongsTo(SpecificationVersion::class, 'current_version_id', 'id');
    }

    // 念のためこちらも明示
    public function changeRequests(){
        return $this->hasMany(SpecChangeRequest::class, 'specification_id', 'id');
    }
}
