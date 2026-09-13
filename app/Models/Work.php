<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    protected $table = 'works';
    protected $primaryKey = 'id_work';
    protected $fillable = 
    [
        'name', 
        'place'
    ];

    //syntax lain
    // protected $primaryKey = 'id_work';
    // protected $guarded = ['id_work'];
}
