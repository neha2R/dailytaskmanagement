<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckoutProducts extends Model
{
    use HasFactory;
    protected $guarded=[];


    public function checkoutConsignement()
    {
        return $this->belongsTo(CheckoutConsignement::class);
    }

    public function product()
    {
        return $this->belongsTo(ProductMaster::class);
    }
}
