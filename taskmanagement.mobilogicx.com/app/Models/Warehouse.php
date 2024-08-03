<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    protected $guarded = [];

    // other relationship
    public function user()
    {
        return $this->hasMany(WhDpMapedUser::class, 'warehouse_id');
    }
}
