<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestStep extends Model
{
    protected $fillable = [
        'test_case_id',
        'step_no',
        'action',
        'expected_result',
    ];

    public function testCase()
    {
        return $this->belongsTo(TestCase::class);
    }
}

