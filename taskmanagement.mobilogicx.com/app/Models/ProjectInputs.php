<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ProjectInputs extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function input()
    {
        return $this->belongsTo(Inputs::class, 'input_id');
    }
    public function uom()
    {
        return $this->belongsTo(Uom::class, 'uom_id');
    }
}
