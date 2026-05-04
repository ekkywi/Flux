<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Environment;

class Project extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'repository_url',
        'branch',
        'stack',
        'build_options',
        'build_command',
        'output_dir',
        'status',
        'description',
    ];

    protected $casts = [
        'status' => 'string',
        'build_options' => 'array',
    ];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->using(ProjectMember::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function environments(): HasMany
    {
        return $this->hasMany(Environment::class);
    }

    public function hasMember(User $user): bool
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function owner(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->wherePivot('role', 'owner')
            ->withTimestamps();
    }

    public function member(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->using(ProjectMember::class)
            ->withPivot('id', 'role')
            ->withTimestamps();
    }

    public function getShortRepoAttributes(): string
    {
        $path = parse_url($this->repository_url, PHP_URL_PATH);
        return trim($path, '/');
    }

    public function getTotalNodesAttribute()
    {
        return $this->environments->count();
    }

    public function getActiveNodesAttribute()
    {
        return $this->environments->where('status', 'running')->count();
    }

    public function getHealthPercentAttribute()
    {
        $total = $this->total_nodes;
        $active = $this->active_nodes;

        return $total > 0 ? round(($active / $total) * 100) : 0;
    }

    public function getHealtColorAttribute()
    {
        $percent = $this->health_percent;

        if ($percent >= 90) return 'text-emerald-400';
        if ($percent >= 50) return 'text-yellow-400';

        return 'text-red-400';
    }
}
