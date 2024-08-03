<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleService extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function parts(){
        return $this->hasMany(VehicleServiceParts::class,'service_id');
    }
    function vehicle(){
        return $this->belongsTo(Vehicles::class,'vehicle_id');
    }
}
