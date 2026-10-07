<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionType extends Model
{
    protected $table = 'position_types';
    protected $primaryKey = 'id_position_type';
    protected $fillable = 
    [
        'name'
    ];
}
