<?php

namespace App\Http\Controllers\api\Trips;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Trip;
use App\Models\TripExpense;
use Carbon\Carbon;
use Facade\FlareClient\Api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    function getActiveExpenses()
    {
        $data = Expense::where('is_active', true)->get();

        if ($data->count()) {
            $responseData = [];
            foreach ($data as $key => $value) {
                $responseData[] = [
                    'id' => $value->id,
                    'name' => $value->name,
                    'iconPath' => $value->iconPath,
                ];
            }
            return ApiResponse::success(['expenses' => $responseData], 'Data retrieved successfully');
        }
        return ApiResponse::notFound('No active expenses found');
    }

    function addExpense(Request $request)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();
    
            // Validate the request
            $validator = Validator::make($request->all(), [
                'trip_id' => 'required',
                'expense_id' => 'required',
                'date' => 'required|date_format:Y-m-d',
                'amount' => 'required|numeric|min:0',
                'payment_mode' => 'required',
                'expense_type' => 'nullable',
                'expense_quantity' => 'nullable',
                'vendor' => 'nullable',
                'place' => 'nullable',
                'document' => 'file|max:1048'
            ],
            [
                'document.file' => 'Invalid file format.',
                'document.max' => 'The file size must not exceed 1MB.',
                'document.mimes' => 'The file must be a JPEG, PNG, or PDF file.',
            ]);
    
            // If validation fails, return an error response
            if ($validator->fails()) {
                $errors['error'] = $validator->errors()->first();
                return ApiResponse::validationError($errors);
            }
            $now = Carbon::now()->format('Y-m-d H:i:s');
    
            // Find the trip and expense based on IDs
            $trip = Trip::find($request->trip_id);
            $expense = Expense::find($request->expense_id);
    
            // Check if the trip exists
            if (!$trip) {
                return ApiResponse::notFound('This trip id does not exist');
            }
    
            // Check if the trip is pending
            if ($trip->status == 'pending') {
                return ApiResponse::notFound('This trip hasn\'t started yet.');
            }
    
            // Check if the expense exists and is active
            if (!$expense) {
                return ApiResponse::notFound('This expense id does not exist');
            }
            if (!$expense->is_active) {
                return ApiResponse::notFound('This expense is not active');
            }
    
            // Add expense and handle file upload
            $file = "";
            if ($request->hasFile('document')) {
                $fileName = 'trip'.$trip->id.'driver'.$trip->driver_id.$request->document->getClientOriginalName() ;
                $file = $request->file('document')->storeAs('TripDocuments', $fileName, 'public');
            }
    
            $create = TripExpense::create([
                'trip_id' => $trip->id,
                'expenses_id' => $request->expense_id,
                'driver_id' => $trip->driver_id,
                'vehicle_id' => $trip->vehicle_id,
                'expense_type' => $request->expense_type,
                'date' => $request->date,
                'payment_mode' => $request->payment_mode,
                'amount' => round((float)$request->amount, 2),
                'quantity' => $request->expense_quantity,
                'vendor' => $request->vendor,
                'location' => $request->place,
                'document_path' => $file,
            ]);
    
            // Commit the database transaction
            DB::commit();
    
            // Prepare data for response
            $data['trip_id'] = $create->trip_id;
            $data['expense_id'] = $create->id;
            $data['date'] = $create->date;
            $data['payment_mode'] = $create->payment_mode;
            $data['amount'] = number_format($create->amount, 2);
    
            // Return a success response
            return ApiResponse::created($data, 'Expense added successfully');
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // Log::error($e->getMessage());
            // Return an internal server error response with the exception message
            return ApiResponse::internalServerError($e->getMessage());
        }
    }
    
}
