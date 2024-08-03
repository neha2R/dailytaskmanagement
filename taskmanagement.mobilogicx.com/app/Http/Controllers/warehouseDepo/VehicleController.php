<?php

namespace App\Http\Controllers\warehouseDepo;

use App\Http\Controllers\Controller;
use App\Models\WhDpMapedUser;
use App\Models\WhDpMappedVehicles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleController extends Controller
{
    public function index()
    {
        $mappedUsers = getMappedUserData();

        if ($mappedUsers->isEmpty()) {
            return redirect()->back()->with('error', 'Oops! It seems you haven\'t been assigned to a warehouse and depot.');
        }

        $warehouseIds = $mappedUsers->pluck('warehouse_id')->filter();
        $depotIds = $mappedUsers->pluck('depo_id')->filter();
        $vehicles = [];

        if ($warehouseIds->isNotEmpty()) {
            $vehicles = WhDpMappedVehicles::whereIn('warehouse_id', $warehouseIds)
                ->whereNull('deassigned_at')
                ->with('vehicle')
                ->get();

            return view('warehouse_head.vehicles', compact('vehicles'));
        } elseif ($depotIds->isNotEmpty()) {
            $vehicles = WhDpMappedVehicles::whereIn('depo_id', $depotIds)
                ->whereNull('deassigned_at')
                ->with('vehicle')
                ->get();

            return view('depot_head.vehicles', compact('vehicles'));
        }
    }
}
