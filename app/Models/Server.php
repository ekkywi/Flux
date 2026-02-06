<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

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
        'ssh_private_key',
    ];

    protected $cast = [
        'deleted_at'        => 'datetime',
        'ssh_private_key'   => 'encrypted',
    ];
}
