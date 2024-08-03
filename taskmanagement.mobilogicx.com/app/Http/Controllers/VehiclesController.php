<?php

namespace App\Http\Controllers;

use App\Models\Consignment;
use App\Models\Trip;
use App\Models\User;
use App\Models\VehicleDocuments;
use App\Models\Vehicles;
use App\Models\VehicleService;
use App\Models\VehicleServiceParts;
use App\Models\VehicleUserHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VehiclesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehicles = Vehicles::orderBy('id', 'desc')->get();
        return view('admin.vehicles.vehicles', compact('vehicles'));
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
            'vehicle_number' =>  ['required', Rule::unique('vehicles', 'vehicle_number')],
            // 'vehicle_body_type' => 'required',
            'vehicle_condition' => 'required',
            'manufacturer_id' => 'required',
            'model_id' => 'required',
            'chassis_number' => 'required',
            'engine_number' => 'required',
            'service_time_duration' => 'required',
            'service_km_duration' => 'required',
            'registration_date' => 'required',
            'validity_date' => 'required',
        ]);

        $data = $request->only([
            'vehicle_body_type' ?? null,
            'vehicle_condition',
            'manufacturer_id',
            'vehicle_color',
            'model_id',
            'vehicle_number',
            'chassis_number',
            'engine_number',
            'wheelbase',
            'service_time_duration',
            'service_km_duration',
        ]);

        $data['registration_date'] = Carbon::createFromFormat('d M Y', $request->registration_date)->format('Y-m-d');
        $data['validity_date'] = Carbon::createFromFormat('d M Y', $request->validity_date)->endOfDay();

        $create = Vehicles::create($data);

        return redirect()->back()->with('success', 'Vehicle has been successfully created!');
    }
    public function checkUniqueVehicleNumber(Request $request)
    {
        $vehicleNumber = $request->input('vehicle_number');
        // Check if the product name is unique
        $isUnique = !Vehicles::where('vehicle_number', $vehicleNumber)->exists();

        return response()->json(['unique' => $isUnique]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Vehicles::with('model')->find($id);
        $data->registration_date = dateformat($data->registration_date, 'd M Y');
        $data->validity_date = dateformat($data->validity_date, 'd M Y');
        $models = getVehiclesModels($data->manufacturer_id);
        return response()->json(['status' => 200, 'data' => $data, 'models' => $models]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // use this method for show vehicle
        // return view('admin.vehicles.show_vehicle');
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
        // return $request;
        $request->validate([
            'id' => 'required',
            // 'vehicle_body_type' => 'required',
            'vehicle_condition' => 'required',
            'manufacturer_id' => 'required',
            'model_id' => 'required',
            'vehicle_number' => 'required',
            'chassis_number' => 'required',
            'engine_number' => 'required',
            'service_time_duration' => 'required',
            'service_km_duration' => 'required',
            'registration_date' => 'required',
            'validity_date' => 'required',
        ]);

        $vehicle = Vehicles::findOrFail($request->id);

        $vehicle->update([
            'vehicle_number' => ['required', Rule::unique('vehicles', 'vehicle_number')->ignore($request->id)],
            'vehicle_body_type' => $request->vehicle_body_type ?? null,
            'vehicle_condition' => $request->vehicle_condition,
            'manufacturer_id' => $request->manufacturer_id,
            'vehicle_color' => $request->vehicle_color,
            'model_id' => $request->model_id,
            'chassis_number' => $request->chassis_number,
            'engine_number' => $request->engine_number,
            'wheelbase' => $request->filled('wheelbase') ? $request->wheelbase : null,
            'registration_date' => Carbon::createFromFormat('d M Y', $request->registration_date)->format('Y-m-d'),
            'validity_date' => Carbon::createFromFormat('d M Y', $request->validity_date)->endOfDay(),
            'service_time_duration' => is_numeric($request->service_time_duration) ? (int) $request->service_time_duration : null,
            'service_km_duration' => is_numeric($request->service_km_duration) ? (int) $request->service_km_duration : null,
        ]);

        return redirect()->back()->with('success', 'Vehicle has been successfully updated!');
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
    public function vehicleDetails($id)
    {
        // Retrieve vehicle details with services
        $vehicle = Vehicles::with(['services' => function ($query) {
            $query->orderBy('id', 'desc');
        }])->find($id);

        // Retrieve trips associated with the vehicle
        $trips = Trip::where('vehicle_id', $vehicle->id)->orderBy('created_at')->get();

        // return $trips;
        // Retrieve vehicle assignment history
        $history = VehicleUserHistory::where('vehicle_id', $vehicle->id)
            ->whereIn('type', ['map', 'unmap'])
            ->orderBy('created_at')
            ->get();

        // Process history to calculate driver durations
        $drivers = $this->calculateDurations($history);

        $drivers = array_reverse($drivers);

        $documents = VehicleDocuments::where('vehicle_id', $vehicle->id)->get();
        // Pass the data to the view
        $data = [
            'vehicle' => $vehicle,
            'trips' => $trips,
            'drivers' => $drivers,
            'documents' => $documents
        ];
        // Return the view with the data
        return view('admin.vehicles.show_vehicle', $data);
    }

    private function calculateDurations($history)
    {
        $processedHistory = [];
        $currentDriver = null;
        $currentAssignmentDate = null;

        foreach ($history as $event) {
            if ($event->type === 'map') {
                // Check if there was a previous driver assignment
                if ($currentDriver !== null) {
                    // Retrieve completed trips for the previous assignment
                    $completedTrips = $this->getCompletedTrips($currentDriver, $currentAssignmentDate, $event->date);

                    // Get driver information from the User model
                    $user = User::find($currentDriver);

                    // Calculate duration between assignments
                    $duration = Carbon::parse($currentAssignmentDate)->diffInMonths(Carbon::parse($event->date));

                    // Add processed data to the result array
                    $processedHistory[] = [
                        'driver_id' => $currentDriver,
                        'driver_name' => $user ? $user->name : '-',
                        'license_no' => $user ? $user->license_no : '-',
                        'from' => $currentAssignmentDate,
                        'to' => $event->date,
                        'duration' => $duration,
                        'completed_trips' => $completedTrips,
                    ];

                    // Reset variables for the next assignment
                    $currentDriver = null;
                    $currentAssignmentDate = null;
                }

                // Set current driver and assignment date
                $currentDriver = $event->user_id;
                $currentAssignmentDate = $event->date;
            } elseif ($event->type === 'unmap') {
                // Check if there was a previous driver assignment
                if ($currentDriver !== null) {
                    // Retrieve completed trips for the previous deassignment
                    $completedTrips = $this->getCompletedTrips($currentDriver, $currentAssignmentDate, $event->date);

                    // Get driver information from the User model
                    $user = User::find($currentDriver);

                    // Calculate duration between assignments
                    $duration = Carbon::parse($currentAssignmentDate)->diffInMonths(Carbon::parse($event->date));

                    // Add processed data to the result array
                    $processedHistory[] = [
                        'driver_id' => $currentDriver,
                        'driver_name' => $user ? $user->name : '-',
                        'license_no' => $user ? $user->license_no : '-',
                        'from' => $currentAssignmentDate,
                        'to' => $event->date,
                        'duration' => $duration,
                        'completed_trips' => $completedTrips,
                    ];

                    // Reset variables for the next deassignment
                    $currentDriver = null;
                    $currentAssignmentDate = null;
                }
            }
        }

        // Include the currently assigned driver if there's no deassignment event
        if ($currentDriver !== null) {
            // Retrieve completed trips for the ongoing assignment
            $completedTrips = $this->getCompletedTrips($currentDriver, $currentAssignmentDate);

            // Get driver information from the User model
            $user = User::find($currentDriver);
            $duration = Carbon::parse($currentAssignmentDate)->diffInMonths(Carbon::now());


            // Add processed data to the result array
            $processedHistory[] = [
                'driver_id' => $currentDriver,
                'driver_name' => $user ? $user->name : '-',
                'license_no' => $user ? $user->license_no : '-',
                'from' => $currentAssignmentDate,
                'to' => null,
                'duration' => $duration,
                'completed_trips' => $completedTrips,
            ];
        }
        return $processedHistory;
    }

    // Helper method to retrieve completed trips within a date range
    private function getCompletedTrips($driverId, $startDate, $endDate = null)
    {
        $query = Trip::where('driver_id', $driverId)
            ->where('status', 'completed')
            ->where('start_date', '>=', $startDate);

        if ($endDate !== null) {
            $query->where('end_date', '<=', $endDate);
        }

        return $query->count();
    }


    public function change_status(Request $request)
    {
        Vehicles::find($request->id)->update(['is_active' => $request->status === 'true' ? true : false]);
        return response()->json(['status' => 200, 'message' => 'Status changed successfully']);
    }

    // service and sechduling
    public function storeServiceDetails(Request $request)
    {
        $validatedData = $request->validate([
            'vehicle_id' => 'required',
            'serviceDate' => 'required|date',
            'odometerReading' => 'required|numeric',
            'kmRun' => 'nullable|numeric',
            'timeGap' => 'nullable|numeric',
            'serviceType' => 'required',
            'serviceAmount' => 'required|numeric',
            'oilChange' => 'required',
            'oilChangeAmount' => 'nullable|numeric',
            'sparePartsChange' => 'nullable',
        ]);

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Use the create method to save the data
            $vehicleService = VehicleService::create([
                'vehicle_id' => $request->vehicle_id,
                'serviceDate' => dateformat($request->serviceDate, 'Y-m-d'),
                'odometerReading' => $request->odometerReading,
                'kmRun' => $request->kmRun,
                'timeGap' => $request->timeGap,
                'serviceType' => $request->serviceType,
                'serviceAmount' => $request->serviceAmount ?? 0,
                'oilChange' => $request->oilChange,
                'oilChangeAmount' => $request->oilChangeAmount ?? 0,
                'sparePartsChange' => $request->sparePartsChange,
                'totalAmount' => $request->totalAmount
            ]);

            // Check if a document is present in the request
            if ($request->hasFile('document')) {
                // Assuming you have a 'documents' directory in your public storage
                $path = $request->file('document')->store('documents', 'public');

                // Save the path or other details to your database
                $vehicleService->update([
                    'document' => $path,
                ]);
            }
            if ($request->has('sparePartsName') && is_array($request->sparePartsName) && !empty($request->sparePartsName)) {
                $partsData = [];        
                foreach ($request->sparePartsName as $key => $partName) {
                    // Check if the 'sparePartsName' is not null and not empty
                    if ($partName !== null && $partName !== '') {
                        // Add part to the partsData array
                        $partsData[] = [
                            'sparePartsName' => $partName,
                            'sparePartsAmount' => $request->sparePartsAmount[$key],
                        ];
                    }
                }
                // Save parts data
                if (!empty($partsData)) {
                    $vehicleService->parts()->createMany($partsData);
                }
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
            return redirect()->back()->with('error', 'Failed to save data.');
        }
    }

    public function getServiceDetails($id)
    {
        // Fetch service details using the $id parameter
        $service = VehicleService::with(['parts', 'vehicle'])->find($id);

        // Return the details as JSON
        return response()->json($service);
    }
}
