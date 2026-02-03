<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMember extends Model
{
    use HasUuids;

    protected $fillable = [
        'project_id',
        'user_id',
        'is_owner'
    ];

    protected function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belonsTo(User::class);
    }
}
