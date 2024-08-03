<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripCheckOutConsignment extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function consignment(){
        return $this->belongsTo(Consignment::class,'consignement_id');
    }
}
