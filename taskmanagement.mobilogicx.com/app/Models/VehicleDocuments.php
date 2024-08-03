<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleDocuments extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'valid_from' => 'date:d M Y',
        'valid_to' => 'date:d M Y',
        'registration_date' => 'date:d M Y',
    ];

    public function attributes()
    {
        return $this->hasMany(VehicleDocumentAttribute::class,'document_id');
    }

    public function vehicle(){
        return $this->belongsTo(Vehicles::class,'vehicle_id');
    }
}
