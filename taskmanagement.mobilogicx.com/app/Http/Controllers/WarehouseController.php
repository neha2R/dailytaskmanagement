<?php

namespace App\Http\Controllers;

use App\Models\Depo;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WhDpMapedUser;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieve users with the role of Warehouse Head and Depot Head
        $warehouseUsers = getUsersByRoleName(['Warehouse Head']);
        $depotUsers = getUsersByRoleName(['Depot Head']);

        // Fetch warehouses and depots that are already mapped to users
        $mappedWarehouses = WhDpMapedUser::whereNull(['deassigned_at', 'depo_id'])->get();
        $mappedDepots = WhDpMapedUser::whereNull(['deassigned_at', 'warehouse_id'])->get();

        // Retrieve available warehouses and depots
        $mappedWarehouseIds = $mappedWarehouses->pluck('warehouse_id')->toArray();
        $availableWarehouses = Warehouse::whereNotIn('id', $mappedWarehouseIds)->get();

        $mappedDepotIds = $mappedDepots->pluck('depo_id')->toArray();
        $availableDepots = Depo::whereNotIn('id', $mappedDepotIds)->get();


        // Fetch all warehouses and depots along with associated users
        $warehouses = Warehouse::orderByDesc('created_at')->get();
        $depots = Depo::orderByDesc('created_at')->get();

        // Return the view with necessary data
        return view('admin.inv_management.warehouse_depo', compact('warehouses', 'depots', 'warehouseUsers', 'depotUsers', 'availableWarehouses', 'availableDepots'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'city' => 'required',
        ]);
        Warehouse::create([
            'name' => $request->name,
            'city' => $request->city,
            'address' => $request->address,
        ]);
        return redirect()->back()->with('success', 'Warehouse added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Warehouse::find($id);
        return response()->json(['status' => 200, 'data' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'city' => 'required',
        ]);
        $depo = Warehouse::find($request->id);
        $depo->update([
            'name' => $request->name,
            'city' => $request->city,
            'address' => $request->address
        ]);
        return redirect()->back()->with('success', 'Wearhouse updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function change_status(Request $request)
    {
        Warehouse::find($request->id)->update(['is_active' => $request->status === 'true' ? true : false]);
        return response()->json(['status' => 200, 'message' => 'Status changed successfully']);
    }
}
