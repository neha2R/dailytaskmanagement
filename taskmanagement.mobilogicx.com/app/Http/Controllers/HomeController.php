<?php

namespace App\Http\Controllers;
use App\Models\ManageTask;
use Carbon\Carbon;

use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function AdminHome()
    {
        $countTotal=Inventory::get()->groupBy('product_id')->count();
        $outOfStockProducts = Inventory::where('quantity', 0)->paginate(10);
        // Retrieve products below the minimum stock level with pagination
        $belowMinLevel = Inventory::whereHas('product', function ($query) {
            $query->where(function ($stockQuery) {
                $stockQuery->where('inventory_type_id', getInventoryTypeBySlug('warehouse'))
                    ->where('quantity', '<=', DB::raw('min_stock_warehouse'))
                    ->where('quantity', '!=', 0);
            })->orWhere(function ($stockQuery) {
                $stockQuery->where('inventory_type_id', getInventoryTypeBySlug('depot'))
                    ->where('quantity', '<=', DB::raw('min_stock_depo'))
                    ->where('quantity', '!=', 0);
            });
        })->paginate(10);
        $date=Carbon::today()->toDateString();

        $taskdata = ManageTask::where('status', 'overdue')->paginate(10);
        $tasktodaydata = ManageTask::where('status', '=' ,'to-do')
        ->where(function($query) use ($date){
             $query->where('startdate','=',$date)
            ->orWhere('enddate','=',$date);
        })->paginate(10);

        // $outOfStockProducts =Inventory::paginate(10);
        // $belowMinLevel =Inventory::paginate(10);
        // return $belowMinLevel;
        return view('dashboard', compact('outOfStockProducts', 'belowMinLevel','countTotal','taskdata','tasktodaydata'));
    }
}
