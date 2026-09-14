<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
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

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }
}