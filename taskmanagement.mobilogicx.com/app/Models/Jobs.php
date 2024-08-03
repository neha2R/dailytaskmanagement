<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobs extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function subtasks()
    {
        return $this->belongsToMany(SubTasks::class, 'sub_tasks_jobs', 'job_id', 'sub_task_id')
        ->withPivot('is_active','id');
    }
    public function inputs()
    {
        return $this->belongsToMany(Inputs::class, 'inputs_jobs', 'job_id', 'input_id')
        ->withPivot('is_active','id');
    }
}
