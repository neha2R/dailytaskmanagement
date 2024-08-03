<?php

namespace App\Http\Controllers\api;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Fule;
use App\Models\User;
use App\Models\Vehicles;
use App\Models\WhDpMappedVehicles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FuelController extends Controller
{
    public function getFuelData(Request $request)
    {
        // Validate the request parameters
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        // If validation fails, return a validation error response
        if ($validator->fails()) {
            $errors['error'] = $validator->errors()->first();
            return ApiResponse::validationError($errors);
        }

        // Check if the user exists
        $user = User::find($request->user_id);

        // If the user is not found, return a not found response
        if (!$user) {
            $errors['error'] = 'User not found';
            return ApiResponse::notFound($errors);
        }

        // Get mapped user data by user ID
        $mappedUsers = getMappedUserDataByUserId($user->id);

        // If the user is not assigned to any warehouse or depot, return a not found response
        if ($mappedUsers->isEmpty()) {
            $errors['error'] = 'Oops! It seems you haven\'t been assigned to a warehouse and depot.';
            return ApiResponse::notFound($errors);
        }

        // Extract warehouse and depot IDs
        $warehouseIds = $mappedUsers->pluck('warehouse_id')->filter();
        $depotIds = $mappedUsers->pluck('depo_id')->filter();

        // Retrieve fuel data based on warehouse or depot IDs
        $fuelsData = $warehouseIds->isNotEmpty()
            ? Fule::whereIn('warehouse_id', $warehouseIds)->orderBy('id', 'desc')->get()
            : Fule::whereIn('depo_id', $depotIds)->orderBy('id', 'desc')->get();

        // Check if there is any fuel data
        if ($fuelsData->isNotEmpty()) {
            // Initialize an empty array to store formatted data
            $formattedData = [];

            // Loop through each fuel data entry and format it
            foreach ($fuelsData as $value) {
                $entry = [
                    'vehicle_number' => $value->vehicle->vehicle_number ?? "",
                    'added_by'       => $value->user->name ?? "",
                    'fuel_qty'       => $value->quantity ?? "",
                    'amount'         => $value->amount ?? "",
                    'date'           => $value->date ?? "",
                ];
                $formattedData[] = $entry;
            }

            // Return a success response with the formatted fuel data
            return ApiResponse::success(['fuel_data' => $formattedData], 'Fuel data retrieved successfully');
        } else {
            // Return a not found response if no fuel data is found
            return ApiResponse::success(['fuel_data' => []], 'Fuel data retrieved successfully');
        }
    }

    public function storeFuelData(Request $request)
    {
        // Validate the request parameters
        $validator = Validator::make($request->all(), [
            'user_id'         => 'required|exists:users,id',
            'vehicle_id'      => 'required',
            'date'            => 'required|date|date_format:Y-m-d',
            'fule_quantity'   => 'required|numeric',
            'amount'          => 'required|numeric',
            'odometerReading' => 'required|numeric',
            'fule_station'    => 'nullable',
            'document'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'document.file'  => 'Invalid file format.',
            'document.max'   => 'The file size must not exceed 1MB.',
            'document.mimes' => 'The file must be a JPEG, PNG, or PDF file.',
        ]);

        // If validation fails, return a validation error response
        if ($validator->fails()) {
            $errors['error'] = $validator->errors()->first();
            return ApiResponse::validationError($errors);
        }

        // Check if the user and vehicle exist
        $user = User::find($request->user_id);
        $vehicle = Vehicles::find($request->vehicle_id);

        if (!$user || !$vehicle) {
            $error = !$user ? 'User not found' : 'Vehicle not found';
            return ApiResponse::notFound(['error' => $error]);
        }

        // Get the latest assigned vehicle
        $assignedVehicle = WhDpMappedVehicles::where('vehicle_id', $request->vehicle_id)
            ->whereNull('deassigned_at')
            ->latest()
            ->first();

        $mappedUsers = getMappedUserDataByUserId($user->id);
        // Extract warehouse and depot IDs
        $warehouseIds = $mappedUsers->pluck('warehouse_id')->filter();
        $depotIds = $mappedUsers->pluck('depo_id')->filter();

        
        if (!in_array($assignedVehicle->warehouse_id, $warehouseIds->toArray()) && $warehouseIds->isNotEmpty()) {
            return ApiResponse::forbidden('Sorry, you are not authorized to access this vehicle. It is not assigned to your warehouse.');
        } elseif (!in_array($assignedVehicle->depo_id, $depotIds->toArray()) && $depotIds->isNotEmpty()) {
            return ApiResponse::forbidden('Sorry, you are not authorized to access this vehicle. It is not assigned to your depot.');
        }


        // If an assigned vehicle is found, proceed with saving fuel data
        if ($assignedVehicle) {
            try {
                // Start a database transaction
                DB::beginTransaction();

                // Create a new fuel record
                $fuel = Fule::create([
                    'warehouse_id'    => $assignedVehicle->warehouse_id ?? null,
                    'depo_id'         => $assignedVehicle->depo_id ?? null,
                    'user_id'         => $user->id,
                    'driver_id'       => optional($assignedVehicle->vehicle->user_vehicle)->user_id,
                    'vehicle_id'      => $request->vehicle_id,
                    'date'            => dateformat($request->date, 'Y-m-d'),
                    'quantity'        => $request->fule_quantity,
                    'amount'          => $request->amount,
                    'odometerReading' => $request->odometerReading,
                    'fule_station'    => $request->fule_station ?? null,
                ]);

                // Check if a document is present in the request
                if ($request->hasFile('document')) {
                    // Save the document to storage
                    $path = $request->file('document')->store('documents', 'public');

                    // Update the fuel record with the document path
                    $fuel->update(['document' => $path]);
                }

                // Commit the transaction if everything is successful
                DB::commit();

                // Build a response with selected attributes
                $response = $fuel->only(['id', 'vehicle_id', 'date', 'quantity', 'amount', 'fule_station']);

                // Return a success response
                return ApiResponse::created($response, 'Fuel data stored successfully');
            } catch (\Exception $e) {
                // Rollback the transaction on exception
                DB::rollBack();

                // Return an internal server error response
                return ApiResponse::internalServerError(['error' => $e->getMessage()], 'Internal Server Error');
            }
        } else {
            // No assigned vehicle found
            return ApiResponse::notFound('Vehicle not assigned to a warehouse or depot');
        }
    }

    public function getAssignedVehicles(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        // If validation fails, return a validation error response
        if ($validator->fails()) {
            $errors['error'] = $validator->errors()->first();
            return ApiResponse::validationError($errors);
        }

        // Check if the user exists
        $user = User::find($request->user_id);

        // If the user is not found, return a not found response
        if (!$user) {
            $errors['error'] = 'User not found';
            return ApiResponse::notFound($errors);
        }

        // Get mapped user data by user ID
        $mappedUsers = getMappedUserDataByUserId($user->id);

        // If the user is not assigned to any warehouse or depot, return a not found response
        if ($mappedUsers->isEmpty()) {
            $errors['error'] = 'Oops! It seems you haven\'t been assigned to a warehouse and depot.';
            return ApiResponse::notFound($errors);
        }

        // Extract warehouse and depot IDs
        $warehouseIds = $mappedUsers->pluck('warehouse_id')->filter();
        $depotIds = $mappedUsers->pluck('depo_id')->filter();
        $vehiclesData = $warehouseIds->isNotEmpty()
            ? WhDpMappedVehicles::whereIn('warehouse_id', $warehouseIds)->whereNull('deassigned_at')->get()
            : WhDpMappedVehicles::whereIn('depo_id', $depotIds)->whereNull('deassigned_at')->get();

        // Check if there is any fuel data
        if ($vehiclesData->isNotEmpty()) {
            // Initialize an empty array to store formatted data
            $formattedData = [];

            // Loop through each fuel data entry and format it
            foreach ($vehiclesData as $value) {
                $entry = [
                    'vehicle_id' => $value->vehicle_id,
                    'vehicle_number' => $value->vehicle->vehicle_number ?? "",
                ];
                $formattedData[] = $entry;
            }

            // Return a success response with the formatted fuel data
            return ApiResponse::success(['vehicles' => $formattedData], 'Vehicles data retrieved successfully');
        } else {
            // Return a not found response if no fuel data is found
            return ApiResponse::success(['vehicles' => []], 'Vehicles data retrieved successfully');

            // return ApiResponse::notFound('No vehicles found');
        }
    }
    public function getFuelStation()
    {
        // Create 10 dummy fuel stations (replace with your actual logic)
        $dummyFuelStations = [];

        for ($i = 1; $i <= 10; $i++) {
            $dummyFuelStations[] = [
                'id' => $i,
                'name' => 'Fuel Station ' . $i,
                'location' => 'Location ' . $i,
                // Add more dummy data as needed
            ];
        }

        // Return a success response with the dummy fuel stations
        return ApiResponse::success(['fuel_stations' => $dummyFuelStations], 'Fuel stations retrieved successfully');
    }
}
