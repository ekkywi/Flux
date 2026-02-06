<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SystemSetting extends Model
{
    use HasUuids;

    protected $fillable = [
        'key_name',
        'public_key',
        'private_key',
        'last_rotated_at',
    ];

    protected $casts = [
        'private_key' => 'encrypted',
        'last_rotated_at' => 'datetime',
    ];
}
