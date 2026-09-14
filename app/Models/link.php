<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class link extends Model
{
    protected $table = 'link';

    protected $primaryKey = 'id_link';

    protected $fillable = [
        'name',
        'link',
    ];
}