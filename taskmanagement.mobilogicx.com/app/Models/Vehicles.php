<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicles extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function model() {
        return $this->belongsTo(VehicleModel::class,'model_id');
    }
    public function user_vehicle() {
        // return $this->hasMany(VehicleUser::class,'vehicle_id')->where('deassigned_at',null)->first();
        return $this->hasOne(VehicleUser::class,'vehicle_id');
    }
    public function maped_warehouse_depo(){
        return $this->hasMany(WhDpMappedVehicles::class,'vehicle_id');
    }
    public function services(){
        return $this->hasMany(VehicleService::class,'vehicle_id');
    }
}
