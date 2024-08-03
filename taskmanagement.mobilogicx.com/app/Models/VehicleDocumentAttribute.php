<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleDocumentAttribute extends Model
{
    use HasFactory;
    protected $guarded = [];



    public function document()
    {
        return $this->belongsTo(VehicleDocuments::class,'document_id');
    }
}
