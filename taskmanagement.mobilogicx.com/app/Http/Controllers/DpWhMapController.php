<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicles;
use App\Models\VehicleUser;
use App\Models\VehicleUserHistory;
use App\Models\WhDpMapedUser;
use App\Models\WhDpMapingHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DpWhMapController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
        $type = $request->warehouse_id ? 'warehouse' : ($request->depo_id ? 'depo' : null);
        if ($type) {
            $rules = [
                $type . '_id' => 'required',
                $type . '_user_id' => 'required',
            ];

            // Validate the request data
            $validatedData = $request->validate($rules);

            try {
                // Start a database transaction
                DB::beginTransaction();

                // Map request data to database column names
                $dataToInsert = [
                    $type . '_id' => $validatedData[$type . '_id'],
                    'user_id' => $validatedData[$type . '_user_id'],
                    'assigned_at' => now()->toDateTimeString(),
                ];
                // Create the model instance and save
                $create = WhDpMapedUser::create($dataToInsert);

                if ($create) {
                    // Create mapping history
                    WhDpMapingHistory::create([
                        $type . '_id' => $validatedData[$type . '_id'],
                        'user_id' => $validatedData[$type . '_user_id'],
                        'type' => 'map',
                        'date' => now(),
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

            if ($type=='depo') {
                $type= 'Depot';
            }
            // Return a success response
            return redirect()->back()->with('success', ucfirst($type) . ' mapped successfully');
        }
    }

    public function multiMapping(Request $request)
    {
        // Validation rules for the incoming request
        $rules = [
            'mapping_type' => 'required|in:warehouse,depot',
            'user_id' => 'required',
            'map_ids' => 'required|array',
        ];

        // Validate the request data
        $validatedData = $request->validate($rules);

        try {
            // Start a database transaction
            DB::beginTransaction();
            $now = now();

            // Loop through each map_id and create a separate record
            foreach ($validatedData['map_ids'] as $mapId) {
                // Map request data to database column names
                $dataToInsert = [
                    'user_id' => $validatedData['user_id'],
                    'assigned_at' => $now,
                ];

                // Check if it's a warehouse or depot mapping
                if ($validatedData['mapping_type'] === 'warehouse') {
                    $dataToInsert['warehouse_id'] = $mapId;
                    $modelClass = WhDpMapedUser::class;
                    $historyType = 'map';

                    // Create the mapping record
                    $create = $modelClass::create($dataToInsert);

                    // Create a history record in warehouse_id table
                    WhDpMapingHistory::create([
                        'warehouse_id' => $dataToInsert['warehouse_id'],
                        'user_id' => $validatedData['user_id'],
                        'type' => $historyType,
                        'date' => $now,
                        'warehouse_id' => $mapId,
                    ]);
                } else {
                    $dataToInsert['depo_id'] = $mapId;
                    $modelClass = WhDpMapedUser::class;
                    $historyType = 'map';

                    // Create the mapping record
                    $create = $modelClass::create($dataToInsert);

                    // Create a history record in depo_id table
                    WhDpMapingHistory::create([
                        'depo_id' => $dataToInsert['depo_id'],
                        'user_id' => $validatedData['user_id'],
                        'type' => $historyType,
                        'date' => $now,
                        'depo_id' => $mapId,
                    ]);
                }
            }

            // Commit the transaction if everything is successful
            DB::commit();
        } catch (\Exception $e) {
            dd($e);
            // If any exception occurs, rollback the transaction
            DB::rollback();
            // Handle the exception (e.g., log, display an error message)
            return redirect()->back()->with('error', 'An error occurred while saving data');
        }

        // Return a success response
        $message = $validatedData['mapping_type'] === 'warehouse' ? 'Warehouse mapped successfully' : 'Depots mapped successfully';
        return redirect()->back()->with('success', $message);
    }

    public function unmap($id, $type)
    {
        if ($type == 'warehouse') {
            try {
                // Start a database transaction
                DB::beginTransaction();
                $now = Carbon::now();
                // Unmap request data to database column names

                $entry = WhDpMapedUser::where('warehouse_id', $id)->get()->last();
                if ($entry) {
                    $entry->update(['deassigned_at' => $now]);
                    WhDpMapingHistory::create([
                        'warehouse_id' => $entry->warehouse_id,
                        'user_id' => $entry->user_id,
                        'type' => 'unmap',
                        'date' => Carbon::now(),
                    ]);
                    $entry->delete();
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
            return redirect()->back()->with('success', 'Warehouse unmapped successfully');
        }
        if ($type == 'depo') {

            try {
                // Start a database transaction
                DB::beginTransaction();
                $now = Carbon::now();
                // Unmap request data to database column names

                $entry = WhDpMapedUser::where('depo_id', $id)->get()->last();
                if ($entry) {
                    $entry->update(['deassigned_at' => $now]);
                    WhDpMapingHistory::create([
                        'warehouse_id' => $entry->warehouse_id,
                        'user_id' => $entry->user_id,
                        'type' => 'unmap',
                        'date' => Carbon::now(),
                    ]);
                    // Commit the transaction if everything is successful
                    DB::commit();
                    $entry->delete();
                }
            } catch (\Exception $e) {
                dd($e);
                // If any exception occurs, rollback the transaction
                DB::rollback();
                // Handle the exception (e.g., log, display an error message)
                return redirect()->back()->with('error', 'An error occurred while saving data ');
            }

            // Return a success response
            return redirect()->back()->with('success', 'Warehouse unmapped successfully');
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


    public function mapingHistory($id, $type)
    {
        // return $id;
        if ($type == 'warehouse') {
            $data = WhDpMapingHistory::where('warehouse_id', $id)->with(['user', 'warehouse'])->orderBy('id', 'desc')->get();
        } elseif ($type == 'depo') {
            $data = WhDpMapingHistory::where('depo_id', $id)->with(['user', 'depo'])->orderBy('id', 'desc')->get();
        }
        if ($data->count()) {
            foreach ($data as $key => $value) {
                $value->date = dateformat($value->date, 'd/m/Y');
            }
            return response()->json(['data' => $data, 'status' => 200]);
        }
        return response()->json(['data' => []]);
    }
}
