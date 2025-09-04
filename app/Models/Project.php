<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function requirements()
    {
        return $this->hasMany(Requirement::class);
    }

    public function testCases()
    {
        return $this->hasMany(TestCase::class);
    }
}
