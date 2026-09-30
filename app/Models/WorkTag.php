<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkTag extends Model
{
    protected $table = 'work_tags';
    protected $primaryKey = 'id_work_tag';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function works(): HasMany
    {
        return $this->hasMany(
            Work::class,
            'id_work_tag',
            'id_work_tag'
        );
    }
}