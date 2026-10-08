<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Stack extends Model
{
    protected $table = 'stack';
    protected $primaryKey = 'id_stack';

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'id_stack_type',
    ];

    public function stackType(): BelongsTo
    {
        return $this->belongsTo(
            StackType::class,
            'id_stack_type',
            'id_stack_type'
        );
    }

    public function experiences(): BelongsToMany
    {
        return $this->belongsToMany(
            Experience::class,
            'experience_stack',
            'id_stack',
            'id_experience'
        );
    }
}