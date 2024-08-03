<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleModel extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function manufacturer(){
        return $this->belongsTo(VehicleManufacturer::class,'manufacturer_id');
    }
}
