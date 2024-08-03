<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMaster extends Model
{
    use HasFactory;
    protected $guarded=[];
    
    public function category(){
        return $this->belongsTo(Categories::class,'category_id');
    }

    public function sub_category(){
        return $this->belongsTo(Categories::class,'sub_category_id');
    }
    public function company(){
        return $this->belongsTo(Companie::class,'company_id');
    }
    public function uom(){
        return $this->belongsTo(Uom::class,'uom_id');
    }
}
