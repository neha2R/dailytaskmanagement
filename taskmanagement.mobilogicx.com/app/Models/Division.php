<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Division extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded=[];


    public function sub_divisions(){
        return $this->hasMany(SubDivision::class,'division_id');
    }
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::saving(function ($model) {
    //         if (!$model->id) {
    //             // Generate a new ID with a prefix and set it to the model
    //             $model->id = 'DIV-' . Str::uuid();
    //         }
    //     });

    //     static::saved(function ($model) {
    //         // You can perform any additional actions after the model is saved here
    //     });
    // }
    
}
