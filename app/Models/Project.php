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

    // 仕様リレーション ← ★ 追加
    public function specifications()
    {
        return $this->hasMany(Specification::class);
    }

    // テストケースリレーション
    public function testCases()
    {
        return $this->hasMany(TestCase::class);
    }

    // /projects/XXXX の XXXX を id で解決する
    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
