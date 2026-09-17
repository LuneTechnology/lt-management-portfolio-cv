<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ExperienceStack extends Pivot
{
    protected $table = 'experience_stack';

    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'id_experience',
        'id_stack',
    ];
}