<?php

namespace App\Http\Controllers\api\Trips;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutValidation;
use App\Models\Consignment;
use App\Models\OdometerFuel;
use App\Models\OdometerFule;
use App\Models\Trip;
use App\Models\TripCheckOutConsignment;
use App\Models\TripCheckOutConsignmentsProduct;
use App\Models\TripDocument;
use App\Models\TripDocumentCheck;
use App\Models\TripHistory;
use App\Models\UnloadedHistory;
use App\Models\UnloadTimer;
use App\Models\User;
use App\Models\Vehicles;
use App\Models\VehicleUser;
use App\Notifications\AndroidNotification;
use App\Notifications\WebNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use stdClass;

class TripController extends Controller
{
    public function getUpcomingTrips(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            $errors['error'] = $validator->errors()->first();
            return ApiResponse::validationError($errors);
        }

        // Check if the user exists
        $user = User::find($request->user_id);

        // Fetch the vehicle data for the user
        $vehicle = VehicleUser::where('user_id', $request->user_id)->first();

        try {
            if ($vehicle) {
                // Fetch upcoming trips based on certain conditions
                $trips = Trip::where('driver_id', $user->id)->where('status', 'pending')->whereDate('start_date', '>=', now()->startOfDay())->get();

                // If upcoming trips are found
                if ($trips->isNotEmpty()) {
                    $data = collect();
                    // Loop through each upcoming trip
                    foreach ($trips as $key => $value) {
                        $tripData = [];
                        $tripData['id'] = $value->id;
                        $tripData['trip_no'] = env('PrefixTrip') . $value->id;
                        $tripData['delivery_type'] = $value->delivery_type;
                        $tripData['origin_location'] = $value->origin_source()->name;
                        $tripData['origin_city'] = $value->origin_source()->city;
                        $tripData['origin_address'] = $value->origin_source()->address;
                        $tripData['start_date'] = dateformat($value->start_date, 'd/m/Y h:i A');
                        $tripData['vehicle_id'] = $value->vehicle_id;
                        $tripData['status'] = $value->status;

                        $tripData['destination_location'] = $value->destination_source()->name;
                        $tripData['destination_city']  = $value->destination_source()->city;
                        $tripData['destination_address']  = $value->destination_source()->address;
                        $tripData['destination_date']  = dateformat($value->end_date, 'd/m/Y');

                        $tripData['delivery_locations'] = $value->trip_items()->orderBy('delivery_date')
                            ->get()->groupBy('destination_source_id')->slice(0, -1)
                            ->map(function ($con) {
                                return [
                                    'location_name' => $con->last()->destination_source()->name,
                                    'location_city' => $con->last()->destination_source()->city,
                                    'location_address' => $con->last()->destination_source()->address,
                                ];
                            })->values()
                            ->toArray();
                        $data->push($tripData);
                    }
                    // Return success response with trip data
                    return ApiResponse::success(["trips" => $data]);
                } else {
                    // If no upcoming trips are found, return appropriate response
                    return ApiResponse::success(['trips' => []], 'trips are not available at the moment.');
                }
            }
            // If no vehicle is found, return appropriate response
            return ApiResponse::forbidden('Vehicle is not mapped');
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // Log::error($e->getMessage());
            // return $e;
            return ApiResponse::internalServerError($e);
        }
    }


    public function getActiveTrips(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
            ]);

            // If validation fails, return error response
            if ($validator->fails()) {
                $errors['error'] = $validator->errors()->first();
                return ApiResponse::validationError($errors);
            }

            // Check if the user exists
            $user = User::find($request->user_id);

            $trips = Trip::where('driver_id', $user->id)->where('status', 'ongoing')->get();
            // return $trips;
            if ($trips->isNotEmpty()) {
                $data = collect();
                // Loop through each upcoming trip
                foreach ($trips as $key => $value) {
                    $tripData = [];
                    $tripData['id'] = $value->id;
                    $tripData['trip_no'] = env('PrefixTrip') . $value->id;
                    $tripData['delivery_type'] = $value->delivery_type;
                    $tripData['origin_location'] = $value->origin_source()->name;
                    $tripData['origin_city'] = $value->origin_source()->city;
                    $tripData['origin_address'] = $value->origin_source()->address;
                    $tripData['start_date'] = dateformat($value->start_date, 'd/m/Y h:i A');
                    $tripData['vehicle_id'] = $value->vehicle_id;
                    $tripData['status'] = $value->status;

                    $tripData['destination_location'] = $value->destination_source()->name;
                    $tripData['destination_city']  = $value->destination_source()->city;
                    $tripData['destination_address']  = $value->destination_source()->address;
                    $tripData['destination_date']  = dateformat($value->end_date, 'd/m/Y');

                    // Process delivery location data
                    $tripData['delivery_locations'] = $value->trip_items()->orderBy('delivery_date')
                        ->get()->groupBy('destination_source_id')->slice(0, -1)
                        ->map(function ($con) {
                            return [
                                'location_name' => $con->last()->destination_source()->name,
                                'location_city' => $con->last()->destination_source()->city,
                                'location_address' => $con->last()->destination_source()->address,
                            ];
                        })->values()
                        ->toArray();
                    $data->push($tripData);
                }
                // Return success response with trip data
                return ApiResponse::success(["trips" => $data]);
            } else {
                // If no upcoming trips are found, return appropriate response
                return ApiResponse::success(['trips' => []], 'trips are not available at the moment.');
            }
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // Log::error($e->getMessage());
            // return $e;
            return ApiResponse::internalServerError($e->getMessage());
        }
    }

    public function startTrip(Request $request)
    {
        // return $request;
        try {
            // Start the database transaction
            DB::beginTransaction();
            // Validate the request
            $validator = Validator::make($request->all(), [
                'trip_id' => 'required',
                'checked_documents_ids' => 'array',
                'odometer_reading' => 'required',
                'fuel_quantity' => 'required',
            ]);
            // If validation fails, return error response
            if ($validator->fails()) {
                $errors['error'] = $validator->errors()->first();
                return ApiResponse::validationError($errors);
            }

            // Find the trip based on the provided trip_id
            $trip = Trip::find($request->trip_id);
            $start_date = Carbon::now()->format('Y-m-d H:i:s');

            // If the trip is found
            if ($trip) {
                // Check the status of the trip
                // if ($trip->status != 'pending') {
                //     return ApiResponse::forbidden('This trip is already started');
                // }
                // if (dateformat($trip->start_date, 'Y-m-d') != dateformat($trip->start_date, 'Y-m-d')) {
                //     return ApiResponse::forbidden('This trip date is in the future');
                // }

                // create documents
                if (isset($request->checked_documents_ids) && is_array($request->checked_documents_ids) && count($request->checked_documents_ids) > 0) {
                    foreach ($request->checked_documents_ids as $key => $value) {
                        TripDocumentCheck::updateOrCreate([
                            'trip_id' => $trip->id,
                            'tripDocId' => $value,
                            'driver_id' => $trip->driver_id,
                            'vehicle_id' => $trip->vehicle_id,
                        ]);
                    }
                }
                if ($request->odometer_reading && $request->fuel_quantity) {
                    // save fuel and meter reading
                    OdometerFuel::updateOrCreate(
                        [
                            'trip_id' => $trip->id,
                            'driver_id' => $trip->driver_id,
                            'vehicle_id' => $trip->vehicle_id,
                        ],
                        [
                            'meter_reading' => $request->odometer_reading,
                            'fuel_quantity' => $request->fuel_quantity,
                            'urea_quantity' => $request->urea_quantity
                        ]
                    );
                }

                // Update the trip status and start date
                $trip->update([
                    'status' => 'ongoing',
                    'started_at' => $start_date,
                ]);

                // add in trip history
                TripHistory::updateOrCreate(
                    [
                        'trip_id' => $trip->id,
                        'vehicle_id' => $trip->vehicle_id,
                        'driver_id' => $trip->driver_id,
                    ],
                    [
                        'start_at' => $start_date
                    ]
                );

                // Commit the transaction
                DB::commit();

                $data['trip_id'] = $trip->id;
                $data['trip_no'] = env('PrefixTrip') . $trip->id;
                $data['status'] = $trip->status;

                $data['start_location'] = $trip->origin_source()->name;
                $data['start_location_city'] = $trip->origin_source()->city;
                $data['start_location_address'] = $trip->origin_source()->address;
                $data['start_date'] = dateformat($trip->start_date, 'd/m/Y h:i A');

                $location = $trip->trip_items()->where('status', 'trip_assigned')->orderBy('delivery_date')->get()->groupBy('destination_source_id');

                $nextLocation = $location->first();

                $data['next_location'] = $nextLocation->last()->destination_source()->name;
                $data['next_location_city'] =  $nextLocation->last()->destination_source()->city;
                $data['next_location_address'] = $nextLocation->last()->destination_source()->address;
                $data['deliveryDate'] = dateformat($nextLocation->last()->delivery_date, 'd/m/Y h:i:s A');

                if ($nextLocation->last()->destination_source_type_id ==  getInventoryTypeBySlug('warehouse')) {
                    $data['user_name'] = $nextLocation->last()->destination_source()->user->last()->name ?? "Not Available";
                    $data['contact'] = $nextLocation->last()->destination_source()->user->last()->mobile ?? "Not Available";
                } elseif ($nextLocation->last()->destination_source_type_id ==  getInventoryTypeBySlug('depot')) {
                    $data['user_name'] = $nextLocation->last()->destination_source()->user->last()->name ?? "Not Available";
                    $data['contact'] = $nextLocation->last()->destination_source()->user->last()->mobile ?? "Not Available";
                } elseif ($nextLocation->last()->destination_source_type_id ==  getInventoryTypeBySlug('site')) {
                    $data['user_name'] = $nextLocation->last()->destination_source()->user->name ?? "Not Available";
                    $data['contact'] = $nextLocation->last()->destination_source()->user->mobile ?? "Not Available";
                }

                $unloading_status = 'pending';
                $unloading_started_at = '';
                $unloading_ended_at = '';

                if ($trip->unload_timer && $trip->unload_timer->isNotEmpty()) {
                    $unloadEntry = $trip->unload_timer()->where(['source_type_id' => $nextLocation->last()->destination_source_type_id, 'source_id' => $nextLocation->last()->destination_source_id])->first();
                    if ($unloadEntry && $unloadEntry->unload_start_at && $unloadEntry->unload_end_at == null) {
                        $unloading_status = 'ongoing';

                        $unloading_started_at = $unloadEntry->unload_start_at;
                        $unloading_ended_at = '';
                    } elseif ($unloadEntry && $unloadEntry->unload_start_at && $unloadEntry->unload_end_at) {
                        $unloading_status = 'completed';

                        $unloading_started_at = $unloadEntry->unload_start_at;
                        $unloading_ended_at = $unloadEntry->unload_end_at;
                    }
                }

                $data['unload_status'] = $unloading_status;
                $data['unloading_started_at'] = $unloading_started_at;
                $data['unloading_ended_at'] = $unloading_ended_at;

                $data['check_out_status'] = false;

                if ($trip->unload_history && $trip->unload_history->isNotEmpty()) {
                    $isCheckOuted = $trip->unload_history->where(['source_type_id' => $nextLocation->last()->destination_source_type_id, 'source_id' => $nextLocation->last()->destination_source_id])->first();
                    if ($isCheckOuted) {
                        $data['check_out_status'] = $isCheckOuted;
                    }
                }

                return ApiResponse::created($data, 'Trip started successfully');
            } else {
                // If the trip is not found, return appropriate response
                return ApiResponse::notFound('This trip id does not exist');
            }
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // Log::error($e->getMessage());
            return $e;
            return ApiResponse::internalServerError($e->getMessage());
        }
    }

    // show trip
    public function showTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            $errors['error'] = $validator->errors()->first();
            return ApiResponse::validationError($errors);
        }

        // Find the trip based on the provided trip_id
        $trip = Trip::find($request->trip_id);
        $now = Carbon::now()->format('Y-m-d H:i:s');

        // If the trip is found
        if ($trip) {
            // Check the status of the trip
            // if ($trip->status != 'pending') {
            //     return ApiResponse::forbidden('This trip is already started');
            // }

            // Group consignments by location

            $tripData = [];
            $tripData['id'] = $trip->id;
            $tripData['trip_no'] = env('PrefixTrip', '') . $trip->id;
            $tripData['delivery_type'] = $trip->delivery_type;
            $tripData['origin_location'] = $trip->origin_source()->name;
            $tripData['city'] = $trip->origin_source()->city;
            $tripData['address'] = $trip->origin_source()->address;
            $tripData['start_date'] = dateformat($trip->start_date, 'd/m/Y h:i A');
            $tripData['status'] = $trip->status;

            $tripData['destination_location'] = $trip->destination_source()->name;
            $tripData['destination_city']  = $trip->destination_source()->city;
            $tripData['destination_address']  = $trip->destination_source()->address;
            $tripData['destination_date']  = dateformat($trip->end_date, 'd/m/Y');


            // Assign delivery location array to trip data
            $tripData['way_points'] = $trip->trip_items()
                ->orderBy('delivery_date')
                ->get()
                ->slice(0, -1) // Exclude the last item
                ->map(function ($con) use ($trip) {
                    return [
                        'location_name' => $con->destination_source()->name,
                        'location_city' => $con->destination_source()->city,
                        'location_address' => $con->destination_source()->address,
                    ];
                });
            return ApiResponse::success($tripData);
        } else {
            return ApiResponse::notFound('trip id is not exist');
        }
    }

    public function tripDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            $errors['error'] = $validator->errors()->first();
            return ApiResponse::validationError($errors);
        }

        // Find the trip based on the provided trip_id
        $trip = Trip::find($request->trip_id);
        $now = Carbon::now()->format('Y-m-d H:i:s');

        // If the trip is found
        if ($trip) {
            // Check the status of the trip
            // if ($trip->status == 'ongoing') {
            //     return ApiResponse::forbidden('This trip is already started');
            // } elseif ($trip->status == 'completed') {
            //     return ApiResponse::forbidden('This trip is completed');
            // }

            $data['id'] = $trip->id;
            $data['trip_no'] = env('PrefixTrip') . $trip->id;
            // Origin details
            $originDetails = [
                'location_name' => $trip->origin_source()->name,
                'location_city' => $trip->origin_source()->city,
                'location_address' => $trip->origin_source()->address,
                'date' => dateformat($trip->start_date, 'd/m/Y h:i A'),
            ];

            $wayPoints = $trip->trip_items()
                ->orderBy('delivery_date')
                ->get()
                ->map(function ($con) use ($trip) {
                    return [
                        'location_name' => $con->destination_source()->name,
                        'location_city' => $con->destination_source()->city,
                        'location_address' => $con->destination_source()->address,
                        'date' => dateformat($trip->delivery_date, 'd/m/Y h:i A'),

                    ];
                })
                ->values()
                ->toArray();

            $data['trip_details'] = array_merge([$originDetails], $wayPoints);


            $data['consignments'] = $trip->trip_items()
                ->orderBy('delivery_date')
                ->get()
                ->map(function ($con) use ($trip) {
                    // return $con->consignements->products->;
                    return [
                        'consignment_id' => $con->consignment_id,
                        'consignementNo' =>  env('PrefixCon') . $con->consignment_id,
                        'products' => $con->consignements->products->map(function ($product) {
                            return [
                                'product_id' => $product->product_id,
                                'product_name' => $product->product->name,
                                'quantity' => $product->quantity,
                            ];
                        }),
                    ];
                });

            $data['trip_advance'] = [];
            return ApiResponse::success($data);
        } else {
            return ApiResponse::notFound('trip id is not exist');
        }
    }

    public function showStartedTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            $errors['error'] = $validator->errors()->first();
            return ApiResponse::validationError($errors);
        }

        // Find the trip based on the provided trip_id
        $trip = Trip::find($request->trip_id);
        $now = Carbon::now()->format('Y-m-d H:i:s');

        // If the trip is found
        if ($trip) {
            $data['trip_id'] = $trip->id;
            $data['trip_no'] = env('PrefixTrip') . $trip->id;
            $data['status'] = $trip->status;
            $data['start_location'] = $trip->origin_source()->name;
            $data['start_location_city'] = $trip->origin_source()->city;
            $data['start_location_address'] = $trip->origin_source()->address;
            $data['start_date'] = dateformat($trip->start_date, 'd/m/Y h:i A');

            $location = $trip->trip_items()->where('status', 'trip_assigned')->orderBy('delivery_date')->get()->groupBy('destination_source_id');
            // Check if $location is empty
            if ($location->isEmpty()) {
                // Set all values to empty strings
                $data['next_location'] = "";
                $data['next_location_city'] = "";
                $data['next_location_address'] = "";
                $data['deliveryDate'] = "";
                $data['user_name'] = "Not Available";
                $data['contact'] = "Not Available";
                $data['unload_status'] = 'pending';
                $data['unloading_started_at'] = '';
                $data['unloading_ended_at'] = '';
                $data['check_out_status'] = false;

                return ApiResponse::success($data);
            }

            $nextLocation = $location->first();

            $data['next_location'] = $nextLocation->last()->destination_source()->name ?? "";
            $data['next_location_city'] =  $nextLocation->last()->destination_source()->city ?? "";
            $data['next_location_address'] = $nextLocation->last()->destination_source()->address ?? "";
            $data['deliveryDate'] = dateformat($nextLocation->last()->delivery_date, 'd/m/Y h:i:s A');

            if ($nextLocation->last()->destination_source_type_id ==  getInventoryTypeBySlug('warehouse')) {
                $data['user_name'] = $nextLocation->last()->destination_source()->user->last()->name ?? "Not Available";
                $data['contact'] = $nextLocation->last()->destination_source()->user->last()->mobile ?? "Not Available";
            } elseif ($nextLocation->last()->destination_source_type_id ==  getInventoryTypeBySlug('depot')) {
                $data['user_name'] = $nextLocation->last()->destination_source()->user->last()->name ?? "Not Available";
                $data['contact'] = $nextLocation->last()->destination_source()->user->last()->mobile ?? "Not Available";
            } elseif ($nextLocation->last()->destination_source_type_id ==  getInventoryTypeBySlug('site')) {
                $data['user_name'] = $nextLocation->last()->destination_source()->user->name ?? "Not Available";
                $data['contact'] = $nextLocation->last()->destination_source()->user->mobile ?? "Not Available";
            }

            $unloading_status = 'pending';
            $unloading_started_at = '';
            $unloading_ended_at = '';

            if ($trip->unload_timer && $trip->unload_timer->isNotEmpty()) {
                $unloadEntry = $trip->unload_timer()->where(['source_type_id' => $nextLocation->last()->destination_source_type_id, 'source_id' => $nextLocation->last()->destination_source_id])->first();
                if ($unloadEntry && $unloadEntry->unload_start_at && $unloadEntry->unload_end_at == null) {
                    $unloading_status = 'ongoing';

                    $unloading_started_at = $unloadEntry->unload_start_at;
                    $unloading_ended_at = '';
                } elseif ($unloadEntry && $unloadEntry->unload_start_at && $unloadEntry->unload_end_at) {
                    $unloading_status = 'completed';

                    $unloading_started_at = $unloadEntry->unload_start_at;
                    $unloading_ended_at = $unloadEntry->unload_end_at;
                }
            }

            $data['unload_status'] = $unloading_status;
            $data['unloading_started_at'] = $unloading_started_at;
            $data['unloading_ended_at'] = $unloading_ended_at;

            $data['check_out_status'] = false;

            if ($trip->unload_history && $trip->unload_history->isNotEmpty()) {
                $isCheckOuted = $trip->unload_history->where(['source_type_id' => $nextLocation->last()->destination_source_type_id, 'source_id' => $nextLocation->last()->destination_source_id])->first();
                if ($isCheckOuted) {
                    $data['check_out_status'] = $isCheckOuted;
                }
            }
            // Process the consignments data
            $consignmentData = $trip->trip_items()->where('status', 'trip_assigned')->orderBy('delivery_date')->get()->groupBy('destination_source_id');

            // Process the unloaded location data
            $unloadLocationData = $consignmentData->first();
            $data['consignments'] = $unloadLocationData->map(function ($con) {
                return [
                    'consignment_id' => $con->consignment_id,
                    'consignementNo' =>  env('PrefixCon') . $con->consignment_id,
                    'products' => $con->consignements->products->map(function ($product) {
                        return [
                            'product_id' => $product->product_id,
                            'product_name' => $product->product->name,
                            'quantity' => $product->quantity,
                        ];
                    }),
                ];
            });
            return ApiResponse::success($data);
        } else {
            return ApiResponse::notFound('trip id is not exist');
        }
    }

    public function startUnloading(Request $request)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();
            // Validate the request
            $validator = Validator::make($request->all(), [
                'trip_id' => 'required',
            ]);

            // If validation fails, return error response
            if ($validator->fails()) {
                $errors['error'] = $validator->errors()->first();
                return ApiResponse::validationError($errors);
            }

            // Find the trip based on the provided trip_id
            $trip = Trip::find($request->trip_id);
            $now = Carbon::now()->format('Y-m-d H:i:s');

            // If the trip is found
            if ($trip) {
                // Check the status of the trip and perform appropriate actions
                if ($trip->status == 'pending') {
                    return ApiResponse::forbidden('This trip hasn\'t started yet.');
                } elseif ($trip->status == 'completed') {
                    return ApiResponse::forbidden('This trip has already been successfully completed.');
                }
                // Process the consignments data
                $consignmentData = $trip->trip_items()->where('status', 'trip_assigned')->orderBy('delivery_date')->get()->groupBy('destination_source_id');

                // Process the unloaded location data
                $unloadLocationData = $consignmentData->first();

                $timer = UnloadTimer::updateOrCreate(
                    [
                        'trip_id' => $trip->id,
                        'source_type_id' => $unloadLocationData->last()->destination_source_type_id,
                        'source_id' => $unloadLocationData->last()->destination_source_id,
                    ],
                    [
                        'unload_start_at' => $now,
                    ]
                );
                $unloading_started_at = dateformat($timer->unload_start_at, 'd/m/Y h:i:s A');

                // Commit the transaction
                DB::commit();

                $data['trip_id'] = $trip->id;
                $data['trip_no'] = env('PrefixTrip') . $trip->id;
                $data['trip_no'] = env('PrefixTrip') . $trip->id;

                $data['unloading_started_at'] = $unloading_started_at;
                $data['unloading_ended_at'] = "";

                $data['consignments'] = $unloadLocationData->map(function ($con) {
                    return [
                        'consignment_id' => $con->consignment_id,
                        'consignementNo' =>  env('PrefixCon') . $con->consignment_id,
                        'products' => $con->consignements->products->map(function ($product) {
                            return [
                                'product_id' => $product->product_id,
                                'product_name' => $product->product->name,
                                'quantity' => $product->quantity,
                            ];
                        }),
                    ];
                });

                return ApiResponse::created($data, 'Unloading Started Successfully');
            } else {
                // If the trip is not found, return appropriate response
                return ApiResponse::notFound('This trip id does not exist');
            }
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // Log::error($e->getMessage());
            // return $e;
            return ApiResponse::internalServerError($e->getMessage());
        }
    }
    public function stopUnloading(Request $request)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();
            // Validate the request
            $validator = Validator::make($request->all(), [
                'trip_id' => 'required',
            ]);

            // If validation fails, return error response
            if ($validator->fails()) {
                $errors['error'] = $validator->errors()->first();
                return ApiResponse::validationError($errors);
            }

            // Find the trip based on the provided trip_id
            $trip = Trip::find($request->trip_id);
            $now = Carbon::now()->format('Y-m-d H:i:s');

            // If the trip is found
            if ($trip) {
                // Check the status of the trip and perform appropriate actions
                if ($trip->status == 'pending') {
                    return ApiResponse::forbidden('This trip hasn\'t started yet.');
                } elseif ($trip->status == 'completed') {
                    return ApiResponse::forbidden('This trip has already been successfully completed.');
                }
                // Process the consignments data
                $consignmentData = $trip->trip_items()->where('status', 'trip_assigned')->orderBy('delivery_date')->get()->groupBy('destination_source_id');

                // Process the unloaded location data
                $unloadLocationData = $consignmentData->first();

                $timer = UnloadTimer::where('trip_id', $trip->id)
                    ->where('source_type_id', $unloadLocationData->last()->destination_source_type_id)
                    ->where('source_id', $unloadLocationData->last()->destination_source_id)
                    ->latest()->first();
                if ($timer) {
                    $timer->update(['unload_end_at' => $now]);
                } else {
                    return ApiResponse::forbidden('Unloading process has not started yet');
                }

                $unloading_started_at = dateformat($timer->unload_start_at, 'd/m/Y h:i:s A');
                $unloading_ended_at = dateformat($timer->unload_end_at, 'd/m/Y h:i:s A');

                // Commit the transaction
                DB::commit();

                $data['trip_id'] = $trip->id;
                $data['trip_no'] = env('PrefixTrip') . $trip->id;
                $data['trip_no'] = env('PrefixTrip') . $trip->id;

                $data['unloading_started_at'] = $unloading_started_at;
                $data['unloading_ended_at'] = $unloading_ended_at;

                $data['consignments'] = $unloadLocationData->map(function ($con) {
                    return [
                        'consignment_id' => $con->consignment_id,
                        'consignementNo' =>  env('PrefixCon') . $con->consignment_id,
                        'products' => $con->consignements->products->map(function ($product) {
                            return [
                                'product_id' => $product->product_id,
                                'product_name' => $product->product->name,
                                'quantity' => $product->quantity,
                            ];
                        }),
                    ];
                });

                return ApiResponse::created($data, 'Unloading Stopped Successfully');
            } else {
                // If the trip is not found, return appropriate response
                return ApiResponse::notFound('This trip id does not exist');
            }
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // Log::error($e->getMessage());
            // return $e;
            return ApiResponse::internalServerError($e->getMessage());
        }
    }

    public function checkoutItems(Request $request)
    {
        // return $request;
        try {
            // Start a database transaction
            DB::beginTransaction();
            // Validate the request
            $validator = Validator::make($request->all(), [
                'trip_id' => 'required',
                'data' => 'required|array|min:1',
                'data.*.products' => 'required|array',
                'data.*.consignment_id' => [
                    'required',
                    'integer',
                    Rule::exists('trip_consignements')->where(function ($query) use ($request) {
                        $query->where('trip_id', $request->input('trip_id'));
                    }),
                    function ($attribute, $value, $fail) use ($request) {
                        // Check if the consignement_id is unique
                        $data = $request->input('data');
                        $count = array_count_values(array_column($data, 'consignment_id'))[$value] ?? 0;
                        if ($count > 1) {
                            $fail('The ' . $attribute . ' must be unique.');
                        }
                    },
                ],
                'data.*.products.*.product_id' => ['required', 'integer'],
                'data.*.products.*.is_checked' => 'required|boolean',
            ]);

            // If validation fails, return error response
            if ($validator->fails()) {
                $errors['error'] = $validator->errors()->first();
                return ApiResponse::validationError($errors);
            }

            $trip = Trip::find($request->trip_id);
            $now = Carbon::now();
            if ($trip) {
                foreach ($request->data as $key => $group) {
                    $consignment = $trip->trip_items()->where('consignment_id', $group['consignment_id'])->first();
                    $create = TripCheckOutConsignment::updateOrCreate([
                        'trip_id' => $request->trip_id,
                        'consignment_id' =>   $consignment->consignment_id,
                        'source_type_id' =>   $consignment->destination_source_type_id,
                        'source_id' => $consignment->destination_source_id,
                    ]);
                    foreach ($group['products'] as $key => $item) {
                        TripCheckOutConsignmentsProduct::updateOrCreate(
                            [
                                "trip_checkout_cn_id" => $create->id,
                                "product_id" => $item['product_id'],
                            ],
                            [
                                "is_checked" => $item['is_checked'],
                            ]
                        );
                    }
                    // update consignments is_unloaded true and status
                    $consignment->update(['status' => 'delivered']);
                    $consignment->consignements->update(['status' => 'delivered']);

                    // Set default user to null
                    $user = null;

                

                    // Check destination source type and assign user accordingly
                    if ($consignment->destination_source_type_id ==  getInventoryTypeBySlug('warehouse')) {
                        $user = $consignment->destination_source()->user->last()->user ?? null;
                        $url= route('whHead.conCheckout',$consignment->consignment_id);
                    } elseif ($consignment->destination_source_type_id ==  getInventoryTypeBySlug('depot')) {
                        $user = $consignment->destination_source()->user->last()->user ?? null;
                        $url= route('dpHead.conCheckout',$consignment->consignment_id);
                    } elseif ($consignment->destination_source_type_id ==  getInventoryTypeBySlug('site')) {
                        $user=$consignment->destination_source()->user;
                        $url= "/";

                    }

                    // Send notification if user is mapped for destination
                    if ($user) {
                        $title = 'Consignment Delivered';
                        $message = 'The consignment ' . env('PrefixCon') . $consignment->consignment_id . ' has been delivered successfully.';

                        $data = [
                            'notification_type' => 'trip',
                            'title' => $title,
                            'message' => $message,
                        ];

                        // Create and send the notification
                        $notification = new AndroidNotification($user, $data);
                        $user->notify($notification);

                        // web notification
                        $user->notify(new WebNotification($url, $title, $message));
                    }
                }

                // Response to send after checkout
                $data['trip_id'] = $trip->id;
                $data['trip_no'] = env('PrefixTrip') . $trip->id;
                $data['status'] = $trip->status;

                $data['start_location'] = $trip->origin_source()->name;
                $data['start_location_city'] = $trip->origin_source()->city;
                $data['start_location_address'] = $trip->origin_source()->address;
                $data['start_date'] = dateformat($trip->start_date, 'd/m/Y h:i A');

                $location = $trip->trip_items()->where('status', 'trip_assigned')->orderBy('delivery_date')->get()->groupBy('destination_source_id');
                // Check if $location is empty
                if ($location->isEmpty()) {

                    $trip->update(['status' => 'completed', 'ended_at' => $now]);

                    if ($update = $trip->trip_history) {
                        $update->update(['end_at' => $now, 'is_completed' => true]);
                    }
                    $data['status'] = $trip->status;

                    // Set all values to empty strings
                    $data['next_location'] = "";
                    $data['next_location_city'] = "";
                    $data['next_location_address'] = "";
                    $data['deliveryDate'] = "";
                    $data['user_name'] = "Not Available";
                    $data['contact'] = "Not Available";
                    $data['unload_status'] = '';
                    $data['unloading_started_at'] = '';
                    $data['unloading_ended_at'] = '';
                    $data['check_out_status'] = false;

                    DB::commit();
                    return ApiResponse::created($data);
                }

                $nextLocation = $location->first();

                $data['next_location'] = $nextLocation->last()->destination_source()->name ?? "";
                $data['next_location_city'] =  $nextLocation->last()->destination_source()->city ?? "";
                $data['next_location_address'] = $nextLocation->last()->destination_source()->address ?? "";
                $data['deliveryDate'] = dateformat($nextLocation->last()->delivery_date, 'd/m/Y h:i:s A');

                if ($nextLocation->last()->destination_source_type_id ==  getInventoryTypeBySlug('warehouse')) {
                    $data['user_name'] = $nextLocation->last()->destination_source()->user->last()->name ?? "Not Available";
                    $data['contact'] = $nextLocation->last()->destination_source()->user->last()->mobile ?? "Not Available";
                } elseif ($nextLocation->last()->destination_source_type_id ==  getInventoryTypeBySlug('depot')) {
                    $data['user_name'] = $nextLocation->last()->destination_source()->user->last()->name ?? "Not Available";
                    $data['contact'] = $nextLocation->last()->destination_source()->user->last()->mobile ?? "Not Available";
                } elseif ($nextLocation->last()->destination_source_type_id ==  getInventoryTypeBySlug('site')) {
                    $data['user_name'] = $nextLocation->last()->destination_source()->user->name ?? "Not Available";
                    $data['contact'] = $nextLocation->last()->destination_source()->user->mobile ?? "Not Available";
                }

                $unloading_status = 'pending';
                $unloading_started_at = '';
                $unloading_ended_at = '';

                if ($trip->unload_timer && $trip->unload_timer->isNotEmpty()) {
                    $unloadEntry = $trip->unload_timer()->where(['source_type_id' => $nextLocation->last()->destination_source_type_id, 'source_id' => $nextLocation->last()->destination_source_id])->first();

                    if ($unloadEntry && $unloadEntry->unload_start_at && $unloadEntry->unload_end_at == null) {
                        $unloading_status = 'ongoing';

                        $unloading_started_at = $unloadEntry->unload_start_at;
                        $unloading_ended_at = '';
                    } elseif ($unloadEntry && $unloadEntry->unload_start_at && $unloadEntry->unload_end_at) {
                        $unloading_status = 'completed';

                        $unloading_started_at = $unloadEntry->unload_start_at;
                        $unloading_ended_at = $unloadEntry->unload_end_at;
                    }
                }

                $data['unload_status'] = $unloading_status;
                $data['unloading_started_at'] = $unloading_started_at;
                $data['unloading_ended_at'] = $unloading_ended_at;

                $data['check_out_status'] = false;

                if ($trip->unload_history && $trip->unload_history->isNotEmpty()) {
                    $isCheckOuted = $trip->unload_history->where(['source_type_id' => $nextLocation->last()->destination_source_type_id, 'source_id' => $nextLocation->last()->destination_source_id])->first();
                    if ($isCheckOuted) {
                        $data['check_out_status'] = $isCheckOuted;
                    }
                }
                DB::commit();
                return ApiResponse::created($data, 'Products checked out successfully.');
            }
            return ApiResponse::notFound('This trip id does not exist');
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // Log::error($e->getMessage());
            return $e;
            return ApiResponse::internalServerError($e->getMessage());
        }
    }

    public function endTrip(Request $request)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();
            // Validate the request
            $validator = Validator::make($request->all(), [
                'trip_id' => 'required',
            ]);

            // If validation fails, return error response
            if ($validator->fails()) {
                $errors['error'] = $validator->errors()->first();
                return ApiResponse::validationError($errors);
            }

            // Find the trip based on the provided trip_id
            $trip = Trip::find($request->trip_id);
            $end_date = Carbon::now()->format('Y-m-d H:i:s');

            // If the trip is found
            if ($trip) {
                // Check the status of the trip and perform appropriate actions
                if ($trip->status == 'pending') {
                    return ApiResponse::forbidden('This trip is not started yet');
                } elseif ($trip->status == 'completed') {
                    return ApiResponse::forbidden('This trip is already completed');
                }

                // Update the trip status and end date
                $trip->update(['status' => 'completed', 'ended_at' => $end_date]);

                if ($update = $trip->trip_history) {
                    $update->update(['end_at' => $end_date, 'is_completed' => true]);
                }

                // Commit the transaction
                DB::commit();

                // Prepare the response data
                $data = [];
                $data['id'] = $trip->id;
                $data['origin_location'] = $trip->origin_source()->name;
                $data['destination_location'] = $trip->destination_source()->name;
                $data['vehicle_no'] = $trip->vehicle->vehicle_number;
                $data['delivery_type'] = $trip->delivery_type;
                $data['status'] = $trip->status;
                $data['started_at'] = dateformat($trip->started_at, 'd/m/Y h:i A');
                $data['ended_at'] = dateformat($trip->ended_at, 'd/m/Y h:i A');

                return ApiResponse::created($data, 'The trip has been successfully completed.');
            } else {
                // If the trip is not found, return appropriate response
                return ApiResponse::notFound('This trip id does not exist');
            }
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // Log::error($e->getMessage());
            return $e;
            return ApiResponse::internalServerError($e);
        }
    }

    public function tripHistories(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
            ]);

            // If validation fails, return error response
            if ($validator->fails()) {
                $errors['error'] = $validator->errors()->first();
                return ApiResponse::validationError($errors);
            }

            // Check if the user exists
            $user = User::find($request->user_id);

            // If user not found, return appropriate response
            if (!$user) {
                $errors['error'] = 'User not found';
                return ApiResponse::notFound($errors);
            }

            $trips = TripHistory::where('driver_id', $user->id)->where('is_completed', 1)->with(['trip'])->get();

            // If upcoming trips are found
            if ($trips->isNotEmpty()) {
                $data = collect();

                // Loop through each upcoming trip
                foreach ($trips as $key => $value) {
                    $tripData = [];
                    $tripData['id'] = $value->trip_id;
                    $tripData['trip_no'] = env('PrefixTrip') . $value->trip_id;
                    $tripData['delivery_type'] = $value->trip->delivery_type;

                    $tripData['origin_location'] = $value->trip->origin_source()->name;
                    $tripData['origin_city'] = $value->trip->origin_source()->city ?? "";
                    $tripData['origin_address'] = $value->trip->origin_source()->address ?? "";

                    $tripData['start_date'] = dateformat($value->trip->start_date, 'd/m/Y');

                    $tripData['destination_location'] = $value->trip->destination_source()->name;
                    $tripData['destination_city']  = $value->trip->destination_source()->city;
                    $tripData['destination_address']  = $value->trip->destination_source()->address;
                    $tripData['destination_date']  = dateformat($value->trip->end_date, 'd/m/Y');

                    $tripData['delivery_locations'] = $value->trip->trip_items()->orderBy('delivery_date')
                        ->get()->groupBy('destination_source_id')->slice(0, -1)
                        ->map(function ($con) {
                            return [
                                'location_name' => $con->last()->destination_source()->name,
                                'location_city' => $con->last()->destination_source()->city,
                                'location_address' => $con->last()->destination_source()->address,
                            ];
                        })
                        ->values()
                        ->toArray();




                    $tripData['expenses'] = $value->trip->expenses->map(function ($expense) {
                        return [
                            // 'id'=>$expense->id,
                            'expenses_id' => $expense->id,
                            'date' => dateformat($expense->date, 'Y-m-d'),
                            'expense_name' => $expense->expense->name ?? "",
                            'expense_name' => $expense->expense->name ?? "",
                            'amount' => $expense->amount ?? "",
                            'payment_mode' => $expense->payment_mode ?? "",
                            'document_path' => $expense->document_path ? asset('storage/' . $expense->document_path) : "",
                        ];
                    });

                    $data->push($tripData);
                }
                // Return success response with trip data
                return ApiResponse::success(["trips" => $data]);
            } else {
                return ApiResponse::success(["trips" => []], 'No trips were found for the user.');
            }
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // Log::error($e->getMessage());
            return $e;
            return ApiResponse::internalServerError($e->getMessage());
        }
    }

    public function getActiveDocuments()
    {
        $documents = TripDocument::where('is_active', true)->get();
        if ($documents->count()) {
            $data = [];
            foreach ($documents as $key => $value) {
                $responseData = new stdClass;
                $responseData->id = $value->id;
                $responseData->document_name = $value->document_name;
                $data[] = $responseData;
            }
            return ApiResponse::success(['documents' => $data]);
        }
        return ApiResponse::success(['documents' => []]);
    }

    public function getCheckedDocuments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            $errors['error'] = $validator->errors()->first();
            return ApiResponse::validationError($errors);
        }

        // Find the trip based on the provided trip_id
        $trip = Trip::find($request->trip_id);
        $now = Carbon::now()->format('Y-m-d H:i:s');


        // If the trip is found
        if ($trip) {
            if ($trip->checked_documents->count()) {
                $data = [];
                foreach ($trip->checked_documents as $key => $value) {
                    $data[] = [
                        'document_id' => $value->tripDocId,
                        'document_name' => $value->document->document_name,
                    ];
                }
                return ApiResponse::success(['documents' => $data]);
            }
            return ApiResponse::success(['documents' => []]);
        } else {
            return ApiResponse::notFound('trip id is not exist');
        }
    }
}
