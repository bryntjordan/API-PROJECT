<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'users';

    protected $fillable = [
        'username', 'email', 'password',
    ];

    protected $hidden = [
        'password',
    ];

    // Jika tabel users kamu tidak punya updated_at
    const UPDATED_AT = null;
}
