<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProjectMember extends Pivot
{
    use HasUuids;

    protected $table = 'project_members';

    protected $fillable = [
        'project_id',
        'user_id',
        'role'
    ];

    public function isManager(): bool
    {
        return in_array($this->role, ['owner', 'manager']);
    }
}
