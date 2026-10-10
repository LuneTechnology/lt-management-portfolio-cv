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
        'social_links',
        'id_role',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];

    protected $appends = ['photo_url'];

    protected function photoUrl(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::get(fn () => $this->photo ? '/storage/' . ltrim($this->photo, '/') : null);
    }

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