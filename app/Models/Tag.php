<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tag extends Model
{
    protected $table = 'tag';
    protected $primaryKey = 'id_tag';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function stacks(): HasMany
    {
        return $this->hasMany(
            Stack::class,
            'id_tag',
            'id_tag'
        );
    }
}