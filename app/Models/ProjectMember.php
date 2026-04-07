<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMember extends Pivot
{
    use HasUuids;

    protected $table = 'project_members';

    protected $fillable = [
        'project_id',
        'user_id',
        'role'
    ];

    public $timestamps = true;

    protected $casts = [
        'role' => 'string',
    ];

    public function isManager(): bool
    {
        return in_array($this->role, ['owner', 'manager']);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
