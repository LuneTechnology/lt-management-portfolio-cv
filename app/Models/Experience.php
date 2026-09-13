<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    protected $table = 'experiences';
    protected $primaryKey = 'id_experience';
    protected $fillable = 
    [
        'id_user',
        'id_work',
        'id_position_type',
        'id_work_type',
        'id_project'
    ];

    //syntax lain
    // protected $primaryKey = 'id_experience';
    // protected $guarded = ['id_experience'];

    public function work() { return $this->belongsTo(Work::class, 'id_work'); }
    public function positionType() { return $this->belongsTo(PositionType::class, 'id_position_type'); }
    public function workType() { return $this->belongsTo(WorkType::class, 'id_work_type'); }
    public function user() { return $this->belongsTo(User::class, 'id_user'); }
    public function project() { return $this->belongsTo(Project::class, 'id_project'); }
}