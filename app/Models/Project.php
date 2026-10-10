<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $table = 'project';
    protected $primaryKey = 'id_project';

    protected $fillable = [
        'name',
        'date_in',
        'date_out',
        'id_category',
        'id_work',
    ];

    protected $casts = [
        'date_in' => 'date:Y-m-d',
        'date_out' => 'date:Y-m-d',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'id_category', 'id_category');
    }

    public function work(): BelongsTo
    {
        return $this->belongsTo(Work::class, 'id_work', 'id_work');
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class, 'id_project', 'id_project');
    }

    public function links(): HasMany
    {
        return $this->hasMany(Link::class, 'id_project', 'id_project');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class, 'id_project', 'id_project');
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class, 'id_project', 'id_project');
    }
}
