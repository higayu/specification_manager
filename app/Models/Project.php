<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // 要件リレーション
    public function requirements()
    {
        return $this->hasMany(Requirement::class);
    }

    // テストケースリレーション
    public function testCases()
    {
        return $this->hasMany(TestCase::class);
    }
}
