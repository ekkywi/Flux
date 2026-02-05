<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeploymentLog extends Model
{
    use HasUuids;

    protected $fillable = [
        'project_environment_id',
        'status',
        'output',
        'duration_seconds'
    ];

    public function environment(): BelongsTo
    {
        return $this->belongsTo(ProjectEnvironment::class, 'project_environment_id');
    }
}
