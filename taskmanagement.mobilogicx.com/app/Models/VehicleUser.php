<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleUser extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user() {
        return $this->belongsTo(User::class,'user_id');
    }
    public function vehicle() {
        return $this->belongsTo(Vehicles::class,'vehicle_id');
    }
    public function vehicle_history() {
        return $this->hasMany(VehicleUserHistory::class,'vehicle_id');
    }
}
