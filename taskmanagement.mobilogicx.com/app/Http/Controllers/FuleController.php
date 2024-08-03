<?php

namespace App\Http\Controllers;

use App\Models\Fule;
use App\Models\Vehicles;
use App\Models\VehicleUser;
use App\Models\WhDpMapedUser;
use App\Models\WhDpMappedVehicles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->role_id == null) {
            $activeVehicles=Vehicles::where('is_active',true)->get();
            $fuelsData = Fule::orderBy('id', 'desc')->get();
            return view('admin.fule_management', compact('fuelsData','activeVehicles'));
        }
        
        $mappedUsers = getMappedUserData();

        if ($mappedUsers->isEmpty()) {
            return redirect()->back()->with('error', 'Oops! It seems you haven\'t been assigned to a warehouse and depot.');
        }

        $warehouseIds = $mappedUsers->pluck('warehouse_id')->filter();
        $depotIds = $mappedUsers->pluck('depo_id')->filter();


        if ($warehouseIds->isNotEmpty()) {
            $assigned_vehicles = WhDpMappedVehicles::whereIn('warehouse_id', $warehouseIds)
                ->whereNull('deassigned_at')
                ->with('vehicle')->get();
            $fuelsData = Fule::whereIn('warehouse_id', $warehouseIds)->orderBy('id', 'desc')->get();

            return view('warehouse_head.fule_management', compact('assigned_vehicles', 'fuelsData'));
        } elseif ($depotIds->isNotEmpty()) {
            $assigned_vehicles = WhDpMappedVehicles::whereIn('depo_id', $depotIds)
                ->whereNull('deassigned_at')
                ->with('vehicle')->get();
            $fuelsData = Fule::whereIn('depo_id', $depotIds)->orderBy('id', 'desc')->get();

            return view('depot_head.fule_management', compact('assigned_vehicles', 'fuelsData'));
        }
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
        // return $request;
        $validatedData = $request->validate([
            'vehicle_id' => 'required',
            'date' => 'required|date',
            'fule_quantity' => 'required|numeric',
            'amount' => 'required|numeric',
            'odometerReading' => 'required|numeric',
            'fule_station' => 'nullable',
            'document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        try {

            $vehicle = WhDpMappedVehicles::where('vehicle_id', $validatedData['vehicle_id'])->whereNull('deassigned_at')->latest()->first();
            $user = Auth::user();

            // Start a database transaction
            DB::beginTransaction();
            // Use the create method to save the data
            $fuel = Fule::create([
                'warehouse_id' => $vehicle->warehouse_id ?? null,
                'depo_id' => $vehicle->depo_id ?? null,
                'user_id' => $user->id,
                'driver_id' => Vehicles::find($validatedData['vehicle_id'])->user_vehicle->user_id ?? null,
                'vehicle_id' => $validatedData['vehicle_id'],
                'date' => dateformat($validatedData['date'], 'Y-m-d'),
                'quantity' => $validatedData['fule_quantity'],
                'amount' => $validatedData['amount'],
                'odometerReading' => $validatedData['odometerReading'],
                'fule_station' => $validatedData['fule_station'] ?? null,
            ]);

            // Check if a document is present in the request
            if ($request->hasFile('document')) {
                // Assuming you have a 'documents' directory in your public storage
                $path = $request->file('document')->store('documents', 'public');

                // Save the path or other details to your database
                $fuel->update([
                    'document' => $path,
                ]);
            }

            // Commit the transaction if everything is successful
            DB::commit();

            // Redirect back with a success message
            return redirect()->back()->with('success', 'Data saved successfully');
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollback();
            return $e;
            // Redirect back with an error message
            return redirect()->back()->with('error', 'Failed to save data. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Fule::with('vehicle', 'user', 'driver', 'warehouse', 'depo')->find($id);
        $data->date = dateformat($data->date, 'd M Y');
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
        //
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
}
