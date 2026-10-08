<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StackType extends Model
{
    protected $table = 'stack_types';
    protected $primaryKey = 'id_stack_type';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function stacks(): HasMany
    {
        return $this->hasMany(
            Stack::class,
            'id_stack_type',
            'id_stack_type'
        );
    }
}