<?php

namespace App\Http\Controllers;

use App\Http\Controllers\api\Trips\TripController as TripsTripController;
use App\Models\Consignment;
use App\Models\DeliveryChallan;
use App\Models\Depo;
use App\Models\EwayBill;
use App\Models\InventoryType;
use App\Models\Pods;
use App\Models\Site;
use App\Models\Trip;
use App\Models\TripConsignement;
use App\Models\TripExpense;
use App\Models\Vehicles;
use App\Models\VehicleUser;
use App\Models\Warehouse;
use App\Notifications\AndroidNotification;
use App\Notifications\TripCreateNotication;
use App\Notifications\TripCreateNotification;
use App\Notifications\WebNotification;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use stdClass;

class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $trips = Trip::orderBy('created_at', 'desc')->get()->take(2);
        // return $trips;
        return view('admin.trips.trip', compact('trips'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $locationTypes = InventoryType::where('is_active', true)->get();

        return view('admin.trips.create_trip', compact('locationTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = $this->validateTripData($request);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Start a database transaction
            DB::beginTransaction();

            $endDate = $this->getMaxDeliveryDate($request);
            $indexOfMaxDate = $this->getIndexOfMaxDate($request, $endDate);

            $destinationLocation = Consignment::find($request->consignments[$indexOfMaxDate]);

            $dataToInsert = $this->mapRequestData($request, $destinationLocation, $endDate);

            $consignementsData = Consignment::whereIn('id', $request->consignments)->get();
            if ($consignementsData->count()) {
                $dataToInsert['delivery_type'] = $this->checkDeliveryType($consignementsData);
            } else {
                return response()->json(['error' => 'Something went wrong'], 500);
            }
            // Use the create function to save the data to the database
            $trip = Trip::create($dataToInsert);

            if ($trip) {
                $this->saveTripConsignments($request, $trip);

                // Send the TripCreateNotification
                $title = 'New Trip Created';
                $message = 'A new trip has been created.';
                $user = $trip->user;
                $data = [
                    'notification_type' => "trip",
                    'title' => $title,
                    'message' => $message,
                    'type' => 2,
                    'deliveryType' => $trip->delivery_type == 'single' ? 1 : 2,
                    'tripId' => $trip->id,
                ];

                // Create and send the notification
                $notification = new AndroidNotification($user, $data);
                $user->notify($notification);

                // Commit the transaction if everything is successful
                DB::commit();

                return response()->json(['status' => 200, 'message' => 'Trip created successfully']);
            }
        } catch (\Exception $e) {
            // If any exception occurs, rollback the transaction
            DB::rollback();

            // Handle the exception (e.g., log, display an error message)
            return response()->json(['error' => 'An error occurred while saving data'], 500);
        }
    }

    protected function validateTripData(Request $request)
    {
        // Validation rules and messages
        $rules = [
            'origin_location_type' => 'required',
            'originLocation' => 'required',
            'start_date' => 'required|date',
            'end_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($request) {
                    $maxDeliveryDate = max($request->input('deliveryDate'));

                    if (strtotime(date('Y-m-d', strtotime($value))) < strtotime(date('Y-m-d', strtotime($maxDeliveryDate)))) {
                        $fail('The trip end date must be after or equal to the maximum delivery date.');
                    }
                },
            ],
            'consignments.*' => 'required|distinct',
            'deliveryDate.*' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $startDate = \Carbon\Carbon::parse($request->input('start_date'))->startOfDay();
                    $endDate = \Carbon\Carbon::parse($request->input('end_date'))->endOfDay();
                    $deliveryDate = \Carbon\Carbon::parse($value);

                    if ($deliveryDate->lt($startDate) || $deliveryDate->gt($endDate)) {
                        $fail('The delivery date must be between the trip start date and end date.');
                    }
                },
            ],
            'vehicle_id' => 'required',
        ];

        $messages = [
            'origin_location_type.required' => 'Please select the origin location type.',
            'originLocation.required' => 'Please select the origin location.',
            'start_date.required' => 'Please select the trip start date.',
            'start_date.date' => 'Invalid date format for trip start date.',
            'end_date.required' => 'Please select the trip end date.',
            'end_date.date' => 'Invalid date format for trip end date.',
            'consignments.*.required' => 'Please select a consignment.',
            'consignments.*.distinct' => 'Consignment cannot be the same.',
            'deliveryDate.*.required' => 'Please enter the delivery date.',
            'vehicle_id.required' => 'Please select a vehicle.',
        ];

        return Validator::make($request->all(), $rules, $messages);
    }

    protected function getMaxDeliveryDate(Request $request)
    {
        return collect($request->deliveryDate)->max(function ($date) {
            return strtotime($date);
        });
    }

    protected function getIndexOfMaxDate(Request $request, $endDate)
    {
        return collect($request->deliveryDate)->search(date('d M Y H:i', $endDate));
    }

    protected function mapRequestData(Request $request, $destinationLocation, $endDate)
    {
        return [
            'origin_source_type_id' => $request->origin_location_type,
            'origin_source_id' => $request->originLocation,
            'destination_source_type_id' => $destinationLocation->destination_source_type_id,
            'destination_source_id' => $destinationLocation->destination_source_id,
            'vehicle_id' => $request->vehicle_id,
            'driver_id' => VehicleUser::where('vehicle_id', $request->vehicle_id)->latest()->first()->user_id,
            'start_date' => dateformat($request->start_date, 'Y-m-d'),
            'end_date' => dateformat($request->end_date, 'Y-m-d'),
        ];
    }

    protected function saveTripConsignments(Request $request, $trip)
    {
        foreach ($request->consignments as $key => $value) {
            $consignment = Consignment::find($value);

            if ($consignment) {
                $dataToInsertConsignments = [
                    'trip_id' => $trip->id,

                    'origin_source_type_id' => $consignment->origin_source_type_id,
                    'origin_source_id' => $consignment->origin_source_id,
                    'destination_source_type_id' => $consignment->destination_source_type_id,
                    'destination_source_id' => $consignment->destination_source_id,

                    'status' => 'trip_assigned',
                    'consignment_id' => $value,
                    'delivery_date' => dateformat($request->deliveryDate[$key], 'Y-m-d H:i:s'),
                ];
                TripConsignement::create($dataToInsertConsignments);
                // Update Consignments status
                $consignment->update(['status' => 'trip_assigned']);
            }
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
        $id = decrypt($id);
        $trip = Trip::find($id);

        $consignments = $trip->trip_items()->orderBy('delivery_date')->get()->groupBy('consignements.destination_source_id');


        $deliveryLocation = [];
        foreach ($consignments as $ind => $group) {

            $responseData = new stdClass();
            $responseData->location_id = $group->first()->consignements->destination_source()->id;
            $responseData->location_name = $group->first()->consignements->destination_source()->name;
            $responseData->location_city = $group->first()->consignements->destination_source()->city;
            $responseData->location_address = $group->first()->consignements->destination_source()->address;
            $responseData->delivery_date = dateformat($group->last()->delivery_date, 'd/m/Y h:i A');
            $responseData->status = $group->first()->consignements->status;
            $deliveryLocation[] = $responseData;
        }
        // challan consignements
        $challans = DeliveryChallan::where('trip_id', $trip->id)->get();
        $challanConsignements = Consignment::whereIn('id', $trip->trip_items->pluck('consignment_id')->all())
            ->whereNotIn('id', array_keys($challans->groupBy('consignment_id')->toArray()))
            ->get()->map(function ($con) {
                return [
                    'id' => $con->id,
                    'con_number' => env('PrefixCon') . $con->id
                ];
            });

        // e-way bill
        $bills = EwayBill::where('trip_id', $trip->id)->get();
        $billsConsignements = Consignment::whereIn('id',  $trip->trip_items->pluck('consignment_id')->all())
            ->whereNotIn('id', array_keys($bills->groupBy('consignment_id')->toArray()))
            ->get()->map(function ($con) {
                return [
                    'id' => $con->id,
                    'con_number' => env('PrefixCon') . $con->id
                ];
            });
        // pods
        $pods = Pods::where('trip_id', $trip->id)->get();
        $data = [
            'trip',
            'deliveryLocation',
            'challans',
            'challanConsignements',
            'bills',
            'billsConsignements',
            'pods',
        ];
        // return $deliveryLocation;
        return view('admin.trips.view_trip', compact($data));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // // return $id;
        // $trip = Trip::with(['trip_items', 'warehouse', 'vehicle', 'user'])->find($id);
        // $trip->start_date = dateformat($trip->start_date, 'd M Y');

        // foreach ($trip->trip_items as $trip_item) {
        //     $trip_item->last_delivery_date =  dateformat($trip_item->last_delivery_date, 'd-M-Y H:i');
        // }

        // $available_consignements = Consignment::where(['warehouse_from_id' => $trip->origin_location, 'status' => 'pending'])->with(['warehouse', 'depo'])->get();

        // $startDate = dateformat($trip->startDate, 'Y-m-d');
        // $endDate = dateformat($trip->end_date_lastloc, 'Y-m-d');
        // $ocupiedVehicles = Trip::where('status', '!=', 'completed')
        //     ->where(function ($query) use ($startDate, $endDate) {
        //         $query->whereBetween('start_date', [$startDate, $endDate])
        //             ->orWhereBetween('end_date_lastloc', [$startDate, $endDate]);
        //     })
        //     ->get()->groupBy('vehicle_id')->toArray();
        // $vehicles = VehicleUser::whereNotIn('vehicle_id', array_keys($ocupiedVehicles))->with('vehicle')->get();
        // if (in_array($trip->vehicle_id, array_keys($ocupiedVehicles))) {
        //     $vehicle = VehicleUser::where('vehicle_id', $trip->vehicle_id)->with('vehicle')->get()->first();
        //     if ($vehicle) {
        //         $vehicles->add($vehicle);
        //     }
        // }

        // return response()->json(['status' => 200, 'data' => $trip, 'available_consignements' => $available_consignements, 'vehicles' => $vehicles]);
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
        // Define the validation rules directly in the controller method
        // $rules = [
        //     'trip_id' => 'required',
        //     'vehicleno' => 'required',
        //     'start_date' => 'required'
        // ];

        // // Validate the request data
        // $validatedData = $request->validate($rules);

        // try {
        //     // Start a database transaction
        //     DB::beginTransaction();

        //     // Map request data to database column names
        //     $dataToInsert = [
        //         'vehicle_id' => $validatedData['vehicleno'],
        //         'start_date' => dateformat($validatedData['start_date'], 'Y-m-d'),
        //     ];
        //     $consignments = array_merge($request->consignments ?? [], array_values($request->old_consignments  ?? []));
        //     $consignementsData = Consignement::whereIn('id', $consignments)->get();

        //     if ($consignementsData->count()) {
        //         $dataToInsert['delivery_type'] = $this->checkDeliveryType($consignementsData);
        //     } else {
        //         return redirect()->back()->with('error', 'Somthing went worng');
        //     }

        //     // Use the create function to save the data to the database
        //     $trip = Trip::find($validatedData['trip_id']);

        //     if ($trip) {
        //         // update date and vehicle
        //         $trip->update($dataToInsert);
        //         // return $request;
        //         if (isset($request->old_consignments) && is_array($request->old_consignments)) {
        //             foreach ($request->old_consignments as $key => $value) {
        //                 $trip_cons = TripConsignement::find($key);
        //                 // return $trip_cons;
        //                 if ($trip_cons->consignement_id != $value) {
        //                     // update previous cons status
        //                     Consignement::find($trip_cons->consignement_id)->update(['status' => 'pending']);

        //                     $trip_cons->update([
        //                         'trip_id' => $trip->id,
        //                         'consignement_id' => $value,
        //                         'last_delivery_date' => dateformat($request->oldDeliveryDate[$key], 'Y-m-d H:i:s'),
        //                     ]);
        //                     // update updating cons status
        //                     Consignement::find($value)->update(['status' => 'trip_assigned', 'last_delivery_date' => dateformat($request->oldDeliveryDate[$key], 'Y-m-d H:i:s')]);
        //                 }
        //             }
        //             // get data they are not exist in array
        //             $deleteData = TripConsignement::where('trip_id', $validatedData['trip_id'])->whereNotIn('id', array_keys($request->old_consignments))->get();
        //             if ($deleteData->isNotEmpty()) {
        //                 foreach ($deleteData as $key => $value) {
        //                     // update status for removeble consignments
        //                     $con = Consignement::find($value->consignement_id);
        //                     if ($con) {
        //                         $con->update(['status' => 'pending', 'last_delivery_date' => null]);
        //                     }
        //                     $value->delete();
        //                 }
        //             }
        //         }
        //         // if new consignment add in this trip
        //         if (isset($request->consignments) && is_array($request->consignments)) {

        //             foreach ($request->consignments as $key => $value) {
        //                 $dataToInsertConsignments = [
        //                     'trip_id' => $trip->id,
        //                     'consignement_id' => $value,
        //                     'last_delivery_date' => dateformat($request->deliveryDate[$key], 'Y-m-d H:i:s'),
        //                 ];
        //                 TripConsignement::create($dataToInsertConsignments);
        //             }
        //             // Update Consignements status
        //             Consignement::whereIn('id', $request->consignments)->update(['status' => 'trip_assigned', 'last_delivery_date' => dateformat($request->deliveryDate[$key], 'Y-m-d H:i:s'),]);
        //         }

        //         $max_last_delivery_date = $trip->trip_items->max('last_delivery_date');

        //         if ($max_last_delivery_date) {
        //             $trip->update(['end_date_lastloc' => dateformat($max_last_delivery_date, 'Y-m-d H:i:s'),]);
        //         }
        //         // Commit the transaction if everything is successful
        //         DB::commit();
        //     }
        // } catch (\Exception $e) {
        //     // If any exception occurs, rollback the transaction
        //     DB::rollback();
        //     // Handle the exception (e.g., log, display an error message)
        //     return $e;
        //     return redirect()->back()->with('error', 'An error occurred while saving data ');
        // }

        // // Return a success response
        // return redirect()->back()->with('success', 'Data updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $trip = Trip::find($id);
            if ($trip) {
                $trip_items = TripConsignement::where('trip_id', $id)->get();
                Consignment::whereIn('id', array_keys($trip_items->groupBy('consignment_id')->toArray()))->update(['status' => 'pending']);
                TripConsignement::where('trip_id', $id)->delete();
                $trip->delete();
            }

            DB::commit();

            return response()->json(['status' => 200, 'message' => 'Your trip has been deleted!']);
        } catch (\Exception $e) {
            DB::rollback();
            // Log the error or handle it accordingly
            return response()->json(['status' => 500, 'message' => 'Something went wrong while deleting the trip. Please try again.']);
        }
    }

    public function getLocations($type)
    {
        //the types are same for inventory 
        if ($type == getInventoryTypeBySlug('warehouse')) {
            $data = Warehouse::where('is_active', true)->orderBy('name')->get();
            return response()->json(['status' => 200, 'data' => $data]);
        }
        if ($type == getInventoryTypeBySlug('depot')) {
            $data = Depo::where('is_active', true)->orderBy('name')->get();
            return response()->json(['status' => 200, 'data' => $data]);
        }
        if ($type == getInventoryTypeBySlug('site')) {
            $data = Site::where('is_active', true)->orderBy('name')->get();
            return response()->json(['status' => 200, 'data' => $data]);
        }
    }
    public function getConDetails($id)
    {
        $cons = Consignment::find($id);

        if (!$cons) {
            return response()->json(['status' => 404, 'message' => 'Consignment not found'], 404);
        }
        $data['destination_location'] = $cons->destination_source()->name;
        $data['delivery_by_date'] = dateformat($cons->delivery_by_date, 'd M Y');

        return response()->json(['status' => 200, 'consignment' => $data]);
    }

    public function getConsignments($id, $type)
    {
        $consignments = Consignment::where('origin_source_type_id', $type)
            ->where('origin_source_id', $id)
            ->where('status', 'pending')
            ->get();

        $formattedConsignments = $consignments->map(function ($con) {
            return [
                'id' => $con->id,
                'name' => env('PrefixCon') . $con->id,
            ];
        });

        return response()->json(['status' => 200, 'consignments' => $formattedConsignments]);
    }

    function checkDeliveryType($data)
    {
        $destinationSourceTypes = [];
        $destinationSourceIds = [];

        foreach ($data as $item) {
            $destinationSourceTypes[] = $item['destination_source_type_id'];
            $destinationSourceIds[] = $item['destination_source_id'];
        }

        // Check if all values in 'destination_source_type_id' are the same
        if (count(array_unique($destinationSourceTypes)) === 1) {
            // Check if all values in 'destination_source_id' are the same
            if (count(array_unique($destinationSourceIds)) === 1) {
                return 'single';
            }
        }

        return 'multi';
    }

    function getVehicles(Request $request)
    {
        $startDate = dateformat($request->startDate, 'Y-m-d');
        $endDate = dateformat($request->endDate, 'Y-m-d');
        $occupiedVehicles = Trip::where('status', '!=', 'completed')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate]);
            })
            ->get()->groupBy('vehicle_id')->toArray();
        $vehicles = VehicleUser::whereNotIn('vehicle_id', array_keys($occupiedVehicles))
            ->with('vehicle')
            ->get()->map(function ($item) {
                return [
                    'id' => $item->vehicle->id,
                    'name' => $item->vehicle->vehicle_number,
                ];
            });

        // if ($request->trip_id) {
        //     $trip = Trip::find($request->id);
        //     if (in_array($trip->vehicle_id, array_keys($occupiedVehicles))) {
        //         $vehicle = VehicleUser::where('vehicle_id', $trip->vehicle_id)->with('vehicle')->get()->first();
        //         if ($vehicle) {
        //             $vehicles->add($vehicle);
        //         }
        //     }
        // }
        if ($vehicles->count()) {
            return response()->json(['status' => 200, 'vehicles' => $vehicles]);
        }
        return response()->json(['status' => 404, 'vehicles' => []]);
    }
    function getDriver($vehicle_id)
    {
        $driver = VehicleUser::where('vehicle_id', $vehicle_id)->with('user')->first();
        if ($driver) {
            return response()->json(['status' => 200, 'driver' => $driver]);
        }
    }

    function storeExpenseDetails(Request $request)
    {
        // return $request;

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Validate the request
            $request->validate(
                [
                    'trip_id' => 'required',
                    'expenseDate' => 'required',
                    'expenseAmount' => 'required|numeric|min:0',
                    'expensePayMode' => 'required',
                    'expenseType' => 'nullable',
                    'expenseDocument' => 'file|max:1048'
                ],
                [
                    'expenseDocument.file' => 'Invalid file format.',
                    'expenseDocument.max' => 'The file size must not exceed 1MB.',
                    'expenseDocument.mimes' => 'The file must be a JPEG, PNG, or PDF file.',
                ]
            );
            $trip = Trip::find($request->trip_id);
            if ($trip) {
                // Add expense and handle file upload
                $file = "";
                if ($request->hasFile('expenseDocument')) {
                    $fileName = 'trip' . $trip->id . 'admin' .  $request->expenseDocument->getClientOriginalName();
                    $file = $request->file('expenseDocument')->storeAs('TripDocuments', $fileName, 'public');
                }

                $create = TripExpense::create([
                    'trip_id' => $trip->id,
                    'expenses_id' => $request->expenseType,
                    'driver_id' => null,
                    'vehicle_id' => $trip->vehicle_id,
                    'date' => dateFormat($request->expenseDate, 'Y-m-d'),
                    'payment_mode' => $request->expensePayMode,
                    'amount' => round((float)$request->expenseAmount, 2),
                    'document_path' => $file,
                ]);

                // Commit the database transaction
                DB::commit();
                return redirect()->back()->with('success', 'Expense details stored successfully.');
            }
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // Log::error($e->getMessage());
            // Return an internal server error response with the exception message
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    function storeChallanDetails(Request $request)
    {
        // return $request;

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Validate the request
            $request->validate(
                [
                    'trip_id' => 'required',
                    'challanDate' => 'nullable',
                    'consignments' => 'required',
                    'materialValue' => 'nullable',
                    'challanDocument' => 'required'
                ],
                [
                    'challanDocument.file' => 'Invalid file format.',
                    'challanDocument.max' => 'The file size must not exceed 1MB.',
                    'challanDocument.mimes' => 'The file must be a JPEG, PNG, or PDF file.',
                ]
            );
            $trip = Trip::find($request->trip_id);
            if ($trip) {
                // Add expense and handle file upload
                $file = "";
                if ($request->hasFile('challanDocument')) {
                    $fileName = 'trip' . $trip->id . 'admin' .  $request->challanDocument->getClientOriginalName();
                    $file = $request->file('challanDocument')->storeAs('TripDocuments', $fileName, 'public');
                }

                $create = DeliveryChallan::create([
                    'trip_id' => $trip->id,
                    'consignement_id' => $request->consignments,
                    'user_id' => Auth::user()->id,
                    'date' => dateFormat($request->challanDate, 'Y-m-d'),
                    'matrial_value' => $request->materialValue,
                    'document_path' => $file,
                ]);
                // Commit the database transaction
                DB::commit();
                return redirect()->back()->with('success', 'Challan saved successfully.');
            }
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // Log::error($e->getMessage());
            // Return an internal server error response with the exception message
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    function storeBillDetails(Request $request)
    {
        // return $request;

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Validate the request
            $request->validate(
                [
                    'trip_id' => 'required',
                    'billDate' => 'nullable',
                    'consignments' => 'required|array',
                    'bill_number' => 'required',
                    'billDocument' => 'required'
                ],
                [
                    'billDocument.file' => 'Invalid file format.',
                    'billDocument.max' => 'The file size must not exceed 1MB.',
                    'billDocument.mimes' => 'The file must be a JPEG, PNG, or PDF file.',
                ]
            );
            $trip = Trip::find($request->trip_id);
            if ($trip) {
                // Add expense and handle file upload
                $file = "";
                if ($request->hasFile('billDocument')) {
                    $fileName = 'trip' . $trip->id . 'admin' .  $request->billDocument->getClientOriginalName();
                    $file = $request->file('billDocument')->storeAs('TripDocuments', $fileName, 'public');
                }
                foreach ($request->consignments as $key => $value) {
                    $create = EwayBill::create([
                        'trip_id' => $trip->id,
                        'consignement_id' => $value,
                        'bill_number' => $request->bill_number,
                        'user_id' => Auth::user()->id,
                        'date' => dateFormat($request->billDate, 'Y-m-d'),
                        'document_path' => $file,
                    ]);
                }
                // Commit the database transaction
                DB::commit();
                return redirect()->back()->with('success', 'Bill saved successfully.');
            }
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // Log::error($e->getMessage());
            // Return an internal server error response with the exception message
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    function getAllCons($trip_id)
    {
        $trip = Trip::find($trip_id);
        if ($trip) {
            $consignments = $trip->trip_items()->orderBy('delivery_date')->get()->map(function ($con) {
                return [
                    'con_number' => env('PrefixCon') . $con->consignment_id,
                    'origin_location' => $con->origin_source()->name,
                    'destination_location' => $con->destination_source()->name,
                    'delivery_date' => dateformat($con->delivery_date, 'd M Y h:i'),
                    'products_count' => $con->consignements->products->count(),
                ];
            });

            return response()->json(['status' => 200, 'consignments' => $consignments]);
        }
        return response()->json(['status' => 400, 'consignments' => [], 'message' => 'Trip is not exist']);
    }

    function getCons($trip_id, $loc_id)
    {
        $trip = Trip::find($trip_id);
        if ($trip) {
            $consignments = $trip->trip_items()->where('destination_source_id', $loc_id)->orderBy('delivery_date')->get()
                ->map(function ($con) {
                    return [
                        'con_number' => env('PrefixCon') . $con->consignment_id,
                        'origin_location' => $con->origin_source()->name,
                        'destination_location' => $con->destination_source()->name,
                        'delivery_date' => dateformat($con->delivery_date, 'd M Y h:i'),
                        'products_count' => $con->consignements->products->count(),
                    ];
                });
            return response()->json(['status' => 200, 'consignments' => $consignments]);
        }
        return response()->json(['status' => 400, 'consignments' => [], 'message' => 'Trip is not exist']);
    }

    function getChallan($id)
    {
        $challan = DeliveryChallan::with('user')->find($id);
        if ($challan) {
            $challan->date = dateformat($challan->date, 'd M Y');
            return response()->json(['status' => 200, 'challan' => $challan]);
        }
        return response()->json(['status' => 400, 'challan' => [], 'message' => 'Challan is not exist']);
    }
    function getBill($id)
    {
        $bill = EwayBill::with('user')->find($id);
        if ($bill) {
            $bill->date = dateformat($bill->date, 'd M Y');
            return response()->json(['status' => 200, 'bill' => $bill]);
        }
        return response()->json(['status' => 400, 'bill' => [], 'message' => 'Bill is not exist']);
    }
    function getExpense($id)
    {
        $expense = TripExpense::with(['expense', 'user'])->find($id);
        if ($expense) {
            $expense->date = dateformat($expense->date, 'd M Y');
            return response()->json(['status' => 200, 'expense' => $expense]);
        }
        return response()->json(['status' => 400, 'expense' => [], 'message' => 'Expense is not exist']);
    }
}
