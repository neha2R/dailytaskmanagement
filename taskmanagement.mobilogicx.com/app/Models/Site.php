<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded=[];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function sub_division(){
        return $this->belongsTo(SubDivision::class,'sub_division_id')->with('division');
    }
}
