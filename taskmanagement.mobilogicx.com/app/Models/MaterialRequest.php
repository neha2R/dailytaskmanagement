<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function products()
    {
        return $this->hasMany(RequestProducts::class, 'material_request_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
