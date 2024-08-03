<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consignment extends Model
{
    use HasFactory;
    protected $guarded=[];
    protected $table='consignements';
    protected $casts = [
        'date'  => 'date:d M Y',
    ];

    function products(){
        return $this->hasMany(ConsignementProducts::class,'consignment_id');
    }

    public function origin_warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'origin_source_id');
    }

    public function origin_depot()
    {
        return $this->belongsTo(Depo::class, 'origin_source_id');
    }

    public function origin_site()
    {
        return $this->belongsTo(Site::class, 'origin_source_id');
    }


    public function origin_source()
    {
        if ($this->origin_source_type_id === getInventoryTypeBySlug('warehouse')) {
            return $this->origin_warehouse;
        } elseif ($this->origin_source_type_id === getInventoryTypeBySlug('depot')) {
            return $this->origin_depot;
        }elseif ($this->origin_source_type_id === getInventoryTypeBySlug('site')) {
            return $this->origin_site;
        }
    }

    public function destination_warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'destination_source_id');
    }

    public function destination_depot()
    {
        return $this->belongsTo(Depo::class, 'destination_source_id');
    }
    public function destination_site()
    {
        return $this->belongsTo(Site::class, 'destination_source_id');
    }

    public function destination_source()
    {
        if ($this->destination_source_type_id === getInventoryTypeBySlug('warehouse')) {
            return $this->destination_warehouse;
        } elseif ($this->destination_source_type_id === getInventoryTypeBySlug('depot')) {
            return $this->destination_depot;
        }elseif ($this->destination_source_type_id === getInventoryTypeBySlug('site')) {
            return $this->destination_site;
        }
    }

    function trip_assigned_cons(){
        return $this->hasOne(TripConsignement::class,'consignment_id');
    }

    function delivery_challan(){
        return $this->hasOne(DeliveryChallan::class,'consignement_id');
    }
    function eway_bill(){
        return $this->hasOne(EwayBill::class,'consignement_id');
    }
    function pods(){
        return $this->hasOne(Pods::class,'consignement_id');
    }

    function checkout_data(){
        return $this->hasOne(CheckoutConsignement::class,'consignment_id');
    }
}
