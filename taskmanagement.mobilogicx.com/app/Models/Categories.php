<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;
    protected $guarded=[];

    function category(){
        return $this->belongsTo(Categories::class,'parent_id');
    }
    function subCategory(){
        return $this->hasMany(Categories::class,'parent_id');
    }
}

