<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestCase extends Model
{
    protected $fillable = [
        'project_id',
        'requirement_id',
        'title',
        'preconditions',
        'expected_result',
        'created_by',
    ];


    public function project()
    {
        return $this->belongsTo(Project::class);
    }


    public function requirement()
    {
        return $this->belongsTo(Requirement::class);
    }

    public function steps()
    {
        return $this->hasMany(TestStep::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

