<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'profile_photo_path',
        'is_active',
        'role_id',
        'department_id',
        'staff_id',
        'position_id',
        'senior_id',
        'emp_type',
        'gender',
        'device_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    function attendance(){
        return $this->hasOne(Attendance::class,'user_id','id');
    }
    function role(){
        return $this->hasOne(Role::class,'id','role_id');
    }
    function position(){
        return $this->hasOne(Position::class,'id','position_id');
    }
    function department(){
        return $this->hasOne(Department::class,'id','department_id');
    }
}
