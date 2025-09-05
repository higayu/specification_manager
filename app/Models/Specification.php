<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Specification extends Model
{
  protected $fillable = ['project_id','code','title','status','current_version_id'];
  public function project(){ return $this->belongsTo(Project::class); }
  public function versions(){ return $this->hasMany(SpecificationVersion::class); }
  public function currentVersion(){ return $this->belongsTo(SpecificationVersion::class, 'current_version_id'); }
  public function changeRequests(){ return $this->hasMany(SpecChangeRequest::class); }
}
