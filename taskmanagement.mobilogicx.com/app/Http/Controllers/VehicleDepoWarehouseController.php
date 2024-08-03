<?php

namespace App\Http\Controllers;

use App\Models\Depo;
use App\Models\Trip;
use App\Models\Vehicles;
use App\Models\Warehouse;
use App\Models\WhDpMappedVehicleHistory;
use App\Models\WhDpMappedVehicles;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleDepoWarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $ocp_depos=WhDpMappedVehicles::where('deassigned_at', null)->get()->groupBy('depo_id')->toArray();
        // $ocp_warehouses=WhDpMappedVehicles::where('deassigned_at', null)->get()->groupBy('warehouse_id')->toArray();
        $ocp_vehicles = WhDpMappedVehicles::where('deassigned_at', null)->get()->groupBy('vehicle_id')->toArray();

        $vehicles = Vehicles::where('is_active', true)->with('maped_warehouse_depo')->get();

        $avl_vehicles = Vehicles::whereNotIn('id', array_keys($ocp_vehicles))->get();
        $depos = Depo::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        // return $vehicles[0]->maped_warehouse_depo->last()->;
        return view('admin.vehicles.vehicle_wh_dp_map', compact('vehicles', 'warehouses', 'depos', 'avl_vehicles'));
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
        if ($request->warehouse_id) {
            $rules = [
                'vc_id' => 'required',
                'warehouse_id' => 'required',
            ];

            // Validate the request data
            $validatedData = $request->validate($rules);

            try {
                // Start a database transaction
                DB::beginTransaction();
                $now = Carbon::now();
                // Map request data to database column names
                $dataToInsert = [
                    'warehouse_id' => $validatedData['warehouse_id'],
                    'vehicle_id' => $validatedData['vc_id'],
                    'assigned_at' => dateformat($now, 'Y-m-d H:i:s')
                ];
                // return $dataToInsert;
                $create = WhDpMappedVehicles::create($dataToInsert);
                if ($create) {
                    WhDpMappedVehicleHistory::create([
                        'warehouse_id' => $validatedData['warehouse_id'],
                        'vehicle_id' => $validatedData['vc_id'],
                        'type' => 'map',
                        'date' => Carbon::now(),
                    ]);
                    // Commit the transaction if everything is successful
                    DB::commit();
                }
            } catch (\Exception $e) {
                dd($e);
                // If any exception occurs, rollback the transaction
                DB::rollback();
                // Handle the exception (e.g., log, display an error message)
                return redirect()->back()->with('error', 'An error occurred while saving data ');
            }

            // Return a success response
            return redirect()->back()->with('success', 'Vehicle mapped successfully');
        }
        if ($request->depo_id) {
            $rules = [
                'vc_id' => 'required',
                'depo_id' => 'required',
            ];

            // Validate the request data
            $validatedData = $request->validate($rules);

            try {
                // Start a database transaction
                DB::beginTransaction();
                $now = Carbon::now();
                // Map request data to database column names
                $dataToInsert = [
                    'depo_id' => $validatedData['depo_id'],
                    'vehicle_id' => $validatedData['vc_id'],
                    'assigned_at' => dateformat($now, 'Y-m-d H:i:s')
                ];
                // return $dataToInsert;
                $create = WhDpMappedVehicles::create($dataToInsert);
                if ($create) {
                    WhDpMappedVehicleHistory::create([
                        'depo_id' => $validatedData['depo_id'],
                        'vehicle_id' => $validatedData['vc_id'],
                        'type' => 'map',
                        'date' => Carbon::now(),
                    ]);
                    // Commit the transaction if everything is successful
                    DB::commit();
                }
            } catch (\Exception $e) {
                dd($e);
                // If any exception occurs, rollback the transaction
                DB::rollback();
                // Handle the exception (e.g., log, display an error message)
                return redirect()->back()->with('error', 'An error occurred while saving data ');
            }

            // Return a success response
            return redirect()->back()->with('success', 'Vehicles mapped successfully');
        }
    }
    public function bulk_store(Request $request)
    {
        // return $request;
        if ($request->warehouse_id) {
            $rules = [
                'warehouse_id' => 'required',
                'vehicles' => 'required|array|min:1',
            ];

            // Validate the request data
            $validatedData = $request->validate($rules);

            try {
                // Start a database transaction
                DB::beginTransaction();
                $now = Carbon::now();

                foreach ($request->vehicles as $key => $value) {
                    // Map request data to database column names
                    $dataToInsert = [
                        'warehouse_id' => $validatedData['warehouse_id'],
                        'vehicle_id' => $value,
                        'assigned_at' => dateformat($now, 'Y-m-d H:i:s')
                    ];
                    // return $dataToInsert;
                    $create = WhDpMappedVehicles::create($dataToInsert);
                    if ($create) {
                        WhDpMappedVehicleHistory::create([
                            'warehouse_id' => $validatedData['warehouse_id'],
                            'vehicle_id' => $value,
                            'type' => 'map',
                            'date' => Carbon::now(),
                        ]);
                    }
                    // Commit the transaction if everything is successful
                    DB::commit();
                }
            } catch (\Exception $e) {
                dd($e);
                // If any exception occurs, rollback the transaction
                DB::rollback();
                // Handle the exception (e.g., log, display an error message)
                return redirect()->back()->with('error', 'An error occurred while saving data ');
            }

            // Return a success response
            return redirect()->back()->with('success', 'Vehicle mapped successfully');
        }
        if ($request->depo_id) {
            $rules = [
                'depo_id' => 'required',
                'vehicles' => 'required|array|min:1',
            ];

            // Validate the request data
            $validatedData = $request->validate($rules);

            try {
                // Start a database transaction
                DB::beginTransaction();
                $now = Carbon::now();
                // Map request data to database column names
                foreach ($request->vehicles as $key => $value) {
                    $dataToInsert = [
                        'depo_id' => $validatedData['depo_id'],
                        'vehicle_id' => $value,
                        'assigned_at' => dateformat($now, 'Y-m-d H:i:s')
                    ];
                    // return $dataToInsert;
                    $create = WhDpMappedVehicles::create($dataToInsert);
                    if ($create) {
                        WhDpMappedVehicleHistory::create([
                            'depo_id' => $validatedData['depo_id'],
                            'vehicle_id' => $value,
                            'type' => 'map',
                            'date' => Carbon::now(),
                        ]);
                        // Commit the transaction if everything is successful
                    }
                }

                DB::commit();
            } catch (\Exception $e) {
                dd($e);
                // If any exception occurs, rollback the transaction
                DB::rollback();
                // Handle the exception (e.g., log, display an error message)
                return redirect()->back()->with('error', 'An error occurred while saving data ');
            }

            // Return a success response
            return redirect()->back()->with('success', 'Vehicles mapped successfully');
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
        //unmap driver with vehicle
        $entry = WhDpMappedVehicles::where('vehicle_id', $id)->where('deassigned_at', null)->first();
        // return $entry; 
        if ($entry) {
            $now = Carbon::now();
            // check this vehicle and user assigned trip
            $checkTrip = Trip::where('vehicle_id', $entry->vehicle_id)
                ->where('driver_id', $entry->user_id)
                ->whereIn('status', ['pending', 'ongoing'])
                ->first();
            if ($checkTrip) {
                if ($checkTrip->status === 'pending') {
                    $errorMessage = 'There is a pending trip associated with this vehicle and driver.';
                } else {
                    $errorMessage = 'There is an ongoing trip associated with this vehicle and driver.';
                }
                return redirect()->back()->with('error', $errorMessage);
            }
            try {
                DB::beginTransaction();
                if ($entry->warehouse_id) {
                    WhDpMappedVehicleHistory::create([
                        'warehouse_id' => $entry->warehouse_id,
                        'vehicle_id' => $entry->vehicle_id,
                        'type' => 'unmap',
                        'date' => Carbon::now(),
                    ]);
                } else {
                    WhDpMappedVehicleHistory::create([
                        'depo_id' => $entry->depo_id,
                        'vehicle_id' => $entry->vehicle_id,
                        'type' => 'unmap',
                        'date' => Carbon::now(),
                    ]);
                }
                $entry->update(['deassigned_at' => $now]);
                DB::commit();
                return redirect()->back()->with('success', 'Vehicle unmapped successfully');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Something went wrong');
            }
        }
        return redirect()->back()->with('error', 'Vehicle not found');
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
    public function mapingHistory($id)
    {
        $data = WhDpMappedVehicleHistory::where('vehicle_id', $id)->with(['vehicle', 'warehouse', 'depo'])->orderBy('id', 'desc')->get();
        if ($data->count()) {
            foreach ($data as $key => $value) {
                $value->date = dateformat($value->date, 'd/m/Y h:i A');
            }
            return response()->json(['data' => $data, 'status' => 200]);
        }
        return response()->json(['data' => []]);
    }
}
