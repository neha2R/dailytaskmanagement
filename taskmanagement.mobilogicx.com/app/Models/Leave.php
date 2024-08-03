<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'start_date'  => 'date:d M Y',
        'end_date' => 'date:d M Y',
    ];

    function user(){
        return $this->hasOne(User::class,'id','user_id');
    }
}
