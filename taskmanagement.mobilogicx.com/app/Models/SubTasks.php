<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubTasks extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function jobs()
    {
        return $this->belongsToMany(Jobs::class,'sub_tasks_jobs', 'sub_task_id', 'job_id')
        ->withPivot('is_active','id');
    }
}
