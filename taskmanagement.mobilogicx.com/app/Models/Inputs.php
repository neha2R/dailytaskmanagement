<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inputs extends Model
{
    use HasFactory;
    protected $guarded = [];
    
    public function jobs()
    {
        return $this->belongsToMany(Jobs::class, 'inputs_jobs', 'input_id', 'job_id')
        ->withPivot('is_active','id');
    }
}
