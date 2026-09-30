<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Education extends Model
{
    protected $table = 'educations';
    protected $primaryKey = 'id_education';

    public $timestamps = false;

    protected $fillable = [
        'id_work',
        'major',
        'level',
        'date_in',
        'date_out',
        'gpa',
        'id_user',
    ];

    protected $casts = [
        'date_in' => 'date',
        'date_out' => 'date',
        'gpa' => 'decimal:2',
    ];

    public function work(): BelongsTo
    {
        return $this->belongsTo(
            Work::class,
            'id_work',
            'id_work'
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'id_user',
            'id_user'
        );
    }
}