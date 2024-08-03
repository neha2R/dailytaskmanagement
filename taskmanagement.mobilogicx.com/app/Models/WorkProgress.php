<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkProgress extends Model
{
    protected $guarded = [];
    use HasFactory;

    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }
    public function sub_task()
    {
        return $this->belongsTo(SubTasks::class, 'sub_task_id');
    }
    public function products()
    {
        return $this->hasMany(WorkProgressProducts::class, 'work_progress_id');
    }
    public function machinery()
    {
        return $this->hasMany(WorkProgressVehicles::class, 'work_progress_id');
    }
}
