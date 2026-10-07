<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PositionType extends Model
{
    protected $table = 'position_types';

    protected $primaryKey = 'id_position_type';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function experiences(): HasMany
    {
        return $this->hasMany(
            Experience::class,
            'id_position_type',
            'id_position_type'
        );
    }
}