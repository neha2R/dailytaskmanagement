<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Companies_categories extends Model
{
    use HasFactory;
    protected $guarded=[];
    function category(){
        return $this->belongsTo(Categories::class,'category_id');
    }
}
