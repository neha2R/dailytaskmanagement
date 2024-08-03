<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventroyHistory extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(ProductMaster::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'source_id');
    }

    public function depot()
    {
        return $this->belongsTo(Depo::class, 'source_id');
    }
    public function site()
    {
        return $this->belongsTo(Site::class, 'source_id');
    }

    public function outwarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'tr_source_id');
    }

    public function outdepot()
    {
        return $this->belongsTo(Depo::class, 'tr_source_id');
    }
    public function outsite()
    {
        return $this->belongsTo(Site::class, 'tr_source_id');
    }


    public function source()
    {
        if ($this->inventory_type_id === getInventoryTypeBySlug('warehouse')) {
            return $this->warehouse;
        } elseif ($this->inventory_type_id === getInventoryTypeBySlug('depot')) {
            return $this->depot;
        }elseif ($this->inventory_type_id === getInventoryTypeBySlug('site')) {
            return $this->site;
        }
    }
    public function outsource()
    {
        if ($this->tr_inventory_type_id === getInventoryTypeBySlug('warehouse')) {
            return $this->outwarehouse;
        } elseif ($this->tr_inventory_type_id === getInventoryTypeBySlug('depot')) {
            return $this->outdepot;
        }elseif ($this->tr_inventory_type_id === getInventoryTypeBySlug('site')) {
            return $this->outsite;
        }
    }
}
