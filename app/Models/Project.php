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
        'default_branch',
        'description',
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

    public function getShortRepoAttributes(): string
    {
        $path = parse_url($this->repository_url, PHP_URL_PATH);
        return trim($path, '/');
    }
}
