<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Work extends Model
{
    protected $table = 'works';
    protected $primaryKey = 'id_work';

    protected $fillable = [
        'name',
        'place',
        'id_work_tag',
    ];

    public function workTag(): BelongsTo
    {
        return $this->belongsTo(
            WorkTag::class,
            'id_work_tag',
            'id_work_tag'
        );
    }

    public function educations(): HasMany
    {
        return $this->hasMany(
            Education::class,
            'id_work',
            'id_work'
        );
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(
            Experience::class,
            'id_work',
            'id_work'
        );
    }
}