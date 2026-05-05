<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Environment extends Model
{
    use HasUuids;

    protected $fillable = [
        'project_id',
        'server_id',
        'db_server_id',
        'port',
        'db_port',
        'name',
        'branch',
        'deploy_script',
        'status',
        'auto_deploy',
        'deployed_commit_hash',
        'latest_commit_hash',
        'url',
        'type',
        'install_ioncube'
    ];

    protected $cast = [
        'install_ioncube' => 'boolean',
        'auto_deply' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function isProduction(): bool
    {
        return $this->type === 'production';
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class);
    }

    public function deployments(): HasMany
    {
        return $this->hasMany(Deployment::class);
    }

    public function dbServer()
    {
        return $this->belongsTo(Server::class, 'db_server_id');
    }

    public function hasUpdate(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->latest_commit_hash && $this->latest_commit_hash !== $this->deployed_commit_hash,
        );
    }

    public function isProd(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->type === 'production',
        );
    }
}
