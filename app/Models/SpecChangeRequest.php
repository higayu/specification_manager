<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class SpecChangeRequest extends Model
{
    protected $fillable = [
        'project_id',
        'specification_id',
        'from_version_id',
        'to_version_id',
        'reason',
        'impact',
        'status',
        'requested_by',
        'approved_by',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function specification(): BelongsTo
    {
        return $this->belongsTo(Specification::class);
    }

    public function fromVersion(): BelongsTo
    {
        return $this->belongsTo(SpecificationVersion::class, 'from_version_id');
    }

    public function toVersion(): BelongsTo
    {
        return $this->belongsTo(SpecificationVersion::class, 'to_version_id');
    }

    public function requestedBy(): BelongsTo
    {
        // App\Models\User に合わせて
        return $this->belongsTo(User::class, 'requested_by');
    }


    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
