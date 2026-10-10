<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Experience extends Model
{
    protected $table = 'experiences';
    protected $primaryKey = 'id_experience';

    protected $fillable = [
        'id_user',
        'id_work',
        'id_position_type',
        'id_work_type',
        'id_project',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function work(): BelongsTo
    {
        return $this->belongsTo(Work::class, 'id_work', 'id_work');
    }

    public function positionType(): BelongsTo
    {
        return $this->belongsTo(PositionType::class, 'id_position_type', 'id_position_type');
    }

    public function workType(): BelongsTo
    {
        return $this->belongsTo(WorkType::class, 'id_work_type', 'id_work_type');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'id_project', 'id_project');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'id_experience', 'id_experience');
    }

    public function stacks(): BelongsToMany
    {
        return $this->belongsToMany(
            Stack::class,
            'experience_stack',
            'id_experience',
            'id_stack',
            'id_experience',
            'id_stack'
        );
    }
}
