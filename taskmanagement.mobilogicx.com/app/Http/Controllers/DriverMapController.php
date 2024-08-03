<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicles;
use App\Models\VehicleUser;
use App\Models\VehicleUserHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DriverMapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $assigned_vehicle = VehicleUser::where('is_active', true)->get()->groupBy('vehicle_id')->toArray();
        $available_vehicles = Vehicles::whereNotIn('id', array_keys($assigned_vehicle))->where('is_active', true)->get();

        $role = Role::where('name', 'driver')->first();
        $assigned_driver = VehicleUser::where('is_active', true)->get()->groupBy('user_id')->toArray();
        $available_drivers = User::whereNotIn('id', array_keys($assigned_driver))->where(['is_active' => true, 'role_id' => $role ? $role->id : null])->get();

        $data = Vehicles::with(['user_vehicle'])->get();
        // return $data;
        return view('admin.vehicles.vehicle_driver_map', compact('data', 'available_vehicles', 'available_drivers'));
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
        $rules = [
            'vehicle_id' => 'required|integer',
            'driver' => 'required|integer',
        ];

        // Validate the request data
        $validatedData = $request->validate($rules);

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Map request data to database column names
            $dataToInsert = [
                'vehicle_id' => $validatedData['vehicle_id'],
                'user_id' => $validatedData['driver'],
            ];
            // return $dataToInsert;
            $create = VehicleUser::create($dataToInsert);
            if ($create) {
                VehicleUserHistory::create([
                    'vehicle_id' => $validatedData['vehicle_id'],
                    'user_id' => $validatedData['driver'],
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //unmap driver with vehicle
        $vehicle = VehicleUser::where('vehicle_id', $id)->first();
        // return $vehicle; 
        if ($vehicle) {
            // check this vehicle and user assigned trip
            $checkTrip = Trip::where('vehicle_id', $vehicle->vehicle_id)
                ->where('driver_id', $vehicle->user_id)
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
                VehicleUserHistory::create([
                    'vehicle_id' => $vehicle->vehicle_id,
                    'user_id' => $vehicle->user_id,
                    'type' => 'unmap',
                    'date' => Carbon::now(),
                ]);
                $vehicle->delete();
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
    }

    public function mapingHistory($id)
    {
        $data = VehicleUserHistory::where('vehicle_id', $id)->with(['user', 'vehicle'])->orderBy('id', 'desc')->get();
        if ($data->count()) {
            foreach ($data as $key => $value) {
                $value->date = dateformat($value->date, 'd/m/Y');
            }
            return response()->json(['data' => $data, 'status' => 200]);
        }
        return response()->json(['data' => []]);
    }
}
