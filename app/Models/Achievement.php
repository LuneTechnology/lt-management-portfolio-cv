<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    protected $primaryKey = 'id_achievement';

    protected $fillable = [
        'name',
        'place',
        'date',
        'id_user',
        'id_project',
        'id_category',
        'description',
        'type',
        'status',
        'logo',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    protected $appends = ['logo_url'];

    protected function logoUrl(): Attribute
    {
        return Attribute::get(
            fn () => $this->logo ? asset('storage/' . $this->logo) : null
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'id_project', 'id_project');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category', 'id_category');
    }
}