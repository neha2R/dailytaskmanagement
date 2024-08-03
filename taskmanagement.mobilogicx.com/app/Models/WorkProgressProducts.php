<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkProgressProducts extends Model
{
    protected $guarded = [];
    use HasFactory;
    public function product()
    {
        return $this->belongsTo(ProductMaster::class, 'product_id');
    }
}
