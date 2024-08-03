<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckoutConsignement extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function checkout_products()
    {
        return $this->hasMany(CheckoutProducts::class,'checkout_consignements_id');
    }
    function consignment (){
        return $this->belongsTo(Consignment::class,'consignment_id');
    }
}
