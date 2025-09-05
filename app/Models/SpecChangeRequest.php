<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SpecChangeRequest extends Model
{
  protected $fillable = ['project_id','specification_id','from_version_id','to_version_id','reason','impact','status','requested_by','approved_by'];
  public function specification(){ return $this->belongsTo(Specification::class); }
  public function fromVersion(){ return $this->belongsTo(SpecificationVersion::class, 'from_version_id'); }
  public function toVersion(){ return $this->belongsTo(SpecificationVersion::class, 'to_version_id'); }
}
