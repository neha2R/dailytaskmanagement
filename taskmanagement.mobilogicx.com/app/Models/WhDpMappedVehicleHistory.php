<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhDpMappedVehicleHistory extends Model
{
    use HasFactory;
    protected $guarded = [];
    function warehouse(){
        return $this->belongsTo(Warehouse::class,'warehouse_id');
    }
    function depo(){
        return $this->belongsTo(Depo::class,'depo_id');
    }
    function vehicle(){
        return $this->belongsTo(Vehicles::class,'vehicle_id');
    }
}
