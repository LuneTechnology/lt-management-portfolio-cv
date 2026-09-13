<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkType extends Model
{
    protected $table = 'work_types';
    protected $primaryKey = 'id_work_type';
    protected $fillable = 
    [
        'name'
    ];

    //syntax lain
    // protected $primaryKey = 'id_work_type';
    // protected $guarded = ['id_work_type'];
}
