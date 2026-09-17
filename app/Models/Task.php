<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $table = 'task';

    protected $primaryKey = 'id_task';

    public $timestamps = false;

    protected $fillable = [
        'id_experience',
        'name',
    ];

    public function experience(): BelongsTo
    {
        return $this->belongsTo(
            Experience::class,
            'id_experience',
            'id_experience'
        );
    }
}