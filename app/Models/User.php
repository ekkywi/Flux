<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasUuids, Notifiable;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'department',
        'role',
        'is_active',
        'last_login_at'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected function cast(): array
    {
        return [
            'password' => 'hashed',
            'last_login-at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }



    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function accessRequest()
    {
        return $this->hasOne(AccessRequest::class);
    }
}
