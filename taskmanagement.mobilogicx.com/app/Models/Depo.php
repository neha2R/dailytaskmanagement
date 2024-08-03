<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depo extends Model
{
    use HasFactory;
    protected $guarded=[];
    
    function warehouse(){
        return $this->belongsTo(Warehouse::class,'warehouse_id');
    }
    public function user()
    {
        return $this->hasMany(WhDpMapedUser::class, 'depo_id');
    }
}
