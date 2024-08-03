<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsignementProducts extends Model
{
    use HasFactory;
    protected $table='consignements_products';
    protected $primary_key='id';
    protected $guarded=[];


    function product(){
        return $this->belongsTo(ProductMaster::class,'product_id');
    }
    function products_with_category(){
        return $this->belongsTo(ProductMaster::class,'product_id')->with('category');
    }
}
