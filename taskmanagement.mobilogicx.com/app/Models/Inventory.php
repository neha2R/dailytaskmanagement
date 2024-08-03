<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;
    use HasFactory;
    protected $table = "inventory";
    protected $primarykey = 'id';

    protected $fillable = [
        'product_id',
        'inventory_type_id',
        'source_id',
        'quantity',
        'model_name',
        'available_quantity'
    ];

    public function product()
    {
        return $this->belongsTo(ProductMaster::class, 'product_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'source_id');
    }

    public function depot()
    {
        return $this->belongsTo(Depo::class, 'source_id');
    }


    public function source()
    {
        if ($this->inventory_type_id === getInventoryTypeBySlug('warehouse')) {
            return $this->warehouse;
        } elseif ($this->inventory_type_id === getInventoryTypeBySlug('depot')) {
            return $this->depot;
        }
    }
    public function history()
    {
        return $this->hasMany(InventroyHistory::class, 'product_id','product_id');
    }
}
