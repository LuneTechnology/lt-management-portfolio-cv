<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    public function experience(): BelongsTo
    {
        return $this->belongsTo(
            Experience::class,
            'id_experience',
            'id_experience'
        );
    }

    public function stack(): BelongsTo
    {
        return $this->belongsTo(
            Stack::class,
            'id_stack',
            'id_stack'
        );
    }
}