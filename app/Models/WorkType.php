<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkType extends Model
{
    protected $table = 'work_types';

    protected $primaryKey = 'id_work_type';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function experiences(): HasMany
    {
        return $this->hasMany(
            Experience::class,
            'id_work_type',
            'id_work_type'
        );
    }
}