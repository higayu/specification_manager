<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'name', 'description'];

    // 要件リレーション
    public function requirements()
    {
        return $this->hasMany(Requirement::class);
    }

    // これで /projects/XXXX の XXXX を id で解決する
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    // テストケースリレーション
    public function testCases()
    {
        return $this->hasMany(TestCase::class);
    }
}
