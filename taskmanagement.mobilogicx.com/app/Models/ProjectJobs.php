<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectJobs extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function job()
    {
        return $this->belongsTo(Jobs::class, 'job_id');
    }
    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }
    public function subdivision()
    {
        return $this->belongsTo(SubDivision::class, 'sub_division_id');
    }
    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }
    public function division_head()
    {
        return $this->belongsTo(User::class, 'division_head_id');
    }
    public function site_head()
    {
        return $this->belongsTo(User::class, 'site_head_id');
    }
    public function subtasks()
    {
        return $this->hasMany(ProjectSubTasks::class, 'projects_jobs_id');
    }
    public function inputs()
    {
        return $this->hasMany(ProjectInputs::class, 'projects_jobs_id');
    }
    public function calculateProgress()
    {
        $totalSubTasks = $this->subtasks->count();
        $completedSubTasks = $this->subtasks()->where('status', 'completed')->count();

        $progress = ($totalSubTasks > 0) ? ($completedSubTasks / $totalSubTasks) * 100 : 0;

        return number_format($progress,2);

    }
}
