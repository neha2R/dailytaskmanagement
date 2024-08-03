<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryMapUnmapDivSubDivSite extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function division(){
        return $this->belongsTo(Division::class,'source_id');
    }
    public function subdivision(){
        return $this->belongsTo(SubDivision::class,'source_id');
    }
    public function site(){
        return $this->belongsTo(Site::class,'source_id');
    }

    public function source()
    {
        if ($this->source_type_id === getInventoryTypeBySlug('division')) {
            return $this->division;
        } elseif ($this->source_type_id === getInventoryTypeBySlug('subdivision')) {
            return $this->subdivision;
        } elseif ($this->source_type_id === getInventoryTypeBySlug('site')) {
            return $this->site;
        }
    }
}
