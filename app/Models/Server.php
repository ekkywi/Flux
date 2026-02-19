<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Server extends Model
{
    use SoftDeletes, HasUuids;

    protected $fillable = [
        'name',
        'ip_address',
        'ssh_port',
        'ssh_user',
        'status',
        'environment',
        'description',
    ];

    protected $cast = [
        'deleted_at' => 'datetime',
    ];

    public function environments(): HasMany
    {
        return $this->hasMany(Environment::class);
    }
}
