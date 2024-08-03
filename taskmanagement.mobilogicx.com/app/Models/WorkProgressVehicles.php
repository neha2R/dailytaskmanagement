<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkProgressVehicles extends Model
{
    protected $guarded = [];
    use HasFactory;
    public function vehicle()
    {
        return $this->belongsTo(Vehicles::class, 'vehicle_id');
    }
}
