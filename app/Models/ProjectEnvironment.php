<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectEnvironment extends Model
{
    use HasUuids;

    protected $fillable = [
        'project_id',
        'name',
        'server_app_id',
        'server_db_id',
        'env_vars',
        'assigned_port'
    ];

    protected $casts = [
        'env_vars' => 'array'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function appServer(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'server_app_id');
    }

    public function dbServer(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'server_db_id');
    }
}
