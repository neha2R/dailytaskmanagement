<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fule extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
    public function driver(){
        return $this->belongsTo(User::class,'driver_id');
    }
    public function vehicle(){
        return $this->belongsTo(Vehicles::class,'vehicle_id');
    }
    public function warehouse(){
        return $this->belongsTo(Warehouse::class,'warehouse_id');
    }
    public function depo(){
        return $this->belongsTo(Depo::class,'depo_id');
    }
}
