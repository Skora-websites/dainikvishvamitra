<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';

    public $timestamps = false;

    protected $primaryKey = 'id';

    protected $fillable = ['name', 'username', 'email', 'role', 'avatar', 'is_active', 'password_hash'];

    protected $hidden = ['password_hash'];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Override: our legacy column is `password_hash`, not `password`.
     */
    public function getAuthPassword(): string
    {
        return (string) $this->password_hash;
    }
}
