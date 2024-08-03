<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestProducts extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function product(){
        return $this->belongsTo(ProductMaster::class,'product_id');
    }
}
