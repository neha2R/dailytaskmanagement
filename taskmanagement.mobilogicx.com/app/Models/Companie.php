<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Companie extends Model
{
    use HasFactory;
    protected $guarded=[];
    function categories () {
        return $this->hasMany(Companies_categories::class,'company_id','id');
    }
}
