<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SpecificationVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'specification_id',
        'version_no',
        'body_md',
        'attributes',
        'created_by',
    ];

    protected $casts = [
        'specification_id' => 'integer',
        'version_no'       => 'integer',
        'attributes'       => 'array',   // JSONを配列で扱う
        'created_by'       => 'integer',
    ];

    public function specification()
    {
        return $this->belongsTo(Specification::class, 'specification_id', 'id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
