<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubDivision extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded=[];
    
    public function division(){
        return $this->belongsTo(Division::class,'division_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function sites(){
        return $this->hasMany(Site::class,'sub_division_id');
    }
}
