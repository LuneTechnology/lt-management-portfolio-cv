<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'category';

    protected $primaryKey = 'id_category';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(
            Project::class,
            'id_category',
            'id_category'
        );
    }
}