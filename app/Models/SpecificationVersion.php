<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SpecificationVersion extends Model
{
  protected $fillable = ['specification_id','version_no','body_md','attributes','created_by'];
  protected $casts = ['attributes'=>'array'];
  public function specification(){ return $this->belongsTo(Specification::class); }
}
