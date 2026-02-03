<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'stack_type',
        'stack_options',
        'description'
    ];

    protected $cast = [
        'stack_options' => 'array',
    ];

    public function environments(): HasMany
    {
        return $this->hasMany(ProjectEnvironment::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('is_owner')
            ->withTimestamps();
    }
}
