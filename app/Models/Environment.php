<?php

namespace App\Models;

use GuzzleHttp\Pool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Environment extends Model
{
    use HasUuids;

    protected $fillable = [
        'project_id',
        'name',
        'branch',
        'url',
        'type',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function isProduction(): bool
    {
        return $this->type === 'production';
    }
}
