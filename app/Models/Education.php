<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $table = 'education';

    protected $primaryKey = 'id_education';

    protected $fillable = [
        'name',
        'major',
        'place',
        'start_date',
        'end_date',
        'gpa',
        'id_user',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'gpa' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}
