<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
    public function jobs()
    {
        return $this->hasMany(ProjectJobs::class, 'project_id');
    }
    // for calculation of project according to job 
    public function calculateProgress()
    {
        $totalJobs = $this->jobs->count();
        $completedJobs = $this->jobs()->where('status', 'completed')->count();

        $progress = ($totalJobs > 0) ? ($completedJobs / $totalJobs) * 100 : 0;
        
        // if ($completedJobs == $totalJobs) {
        //     $this->update(['status' => 'completed']);
        // }
        return number_format($progress,2);
    }
}
