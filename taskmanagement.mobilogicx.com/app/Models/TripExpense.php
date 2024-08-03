<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripExpense extends Model
{
    use HasFactory;
    protected $guarded=[];

    public function expense(){
        return $this->belongsTo(Expense::class,'expenses_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'driver_id');
    }
}
