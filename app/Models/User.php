<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $primaryKey = 'id_user';

    protected $fillable = [
        'email',
        'password',
        'username',
        'photo',
        'contact',
        'aboutme',
        'id_role',
    ];

    protected $hidden = [
        'password',
    ];

    public function role()
    {
        return $this->belongsTo(
            Role::class,
            'id_role',
            'id_role'
        );
    }
}