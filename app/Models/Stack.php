<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Stack extends Model
{
    protected $table = 'stack';

    protected $primaryKey = 'id_stack';

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'tag',
    ];

    public function experiences(): BelongsToMany
    {
        return $this->belongsToMany(
            Experience::class,
            'experience_stack',
            'id_stack',
            'id_experience'
        );
    }
}