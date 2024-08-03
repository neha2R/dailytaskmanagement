<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSubTasks extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function subtask()
    {
        return $this->belongsTo(SubTasks::class, 'sub_task_id');
    }
    public function projectJob()
    {
        return $this->belongsTo(ProjectJobs::class, 'projects_jobs_id');
    }

    public function progress()
    {
        return $this->hasMany(WorkProgress::class, 'project_sub_task_id');
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
}
