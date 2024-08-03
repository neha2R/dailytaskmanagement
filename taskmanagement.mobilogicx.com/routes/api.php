<?php

use App\ApiResponse;
use App\Http\Controllers\api\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\AttendanceController;
use App\Http\Controllers\api\FuelController;
use App\Http\Controllers\api\inventory\InventoryAndConsignmentController as WarehouseHeadAndDepotHeadInventoryAndConsignmentController;
use App\Http\Controllers\api\LeaveController;
use App\Http\Controllers\api\project_management\InventoryAndConsignmentController;
use App\Http\Controllers\api\project_management\ProgressReportController;
use App\Http\Controllers\api\project_management\ProjectManagementController;
use App\Http\Controllers\api\task_management\TaskManagementController;
use App\Http\Controllers\api\Trips\ExpenseController;
use App\Http\Controllers\api\Trips\TripController;
use App\Models\Uom;
use App\Models\User;
use PHPUnit\Framework\Test;

use function PHPUnit\Framework\isEmpty;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// auth routes
Route::post('/Login', [AuthenticationController::class, 'login']);
Route::post('/VerifyOtp', [AuthenticationController::class, 'verify_otp'])->name('VerifyOtp');
Route::post('/Resend', [AuthenticationController::class, 'resend_otp'])->name('resend_otp');
Route::get('/privacy-policy', [AuthenticationController::class, 'privacypolicy']);

Route::group(['middleware' => []], function () {
    // Default routes
    Route::post('/GetDepartment', [UserController::class, 'get_department']);
    Route::post('/GetRole', [UserController::class, 'get_role']);
    Route::post('/checkstatus', [UserController::class, 'check_status']);

    Route::post('/update-fcm-token', [UserController::class, 'updateFcmToken']);


    // profile routes
    Route::post('/GetProfile', [UserController::class, 'get_profile']);
    Route::post('/UpdateProfile', [UserController::class, 'update_profile']);


    //attendance module
    Route::post('/GetAttendance', [AttendanceController::class, 'get_attendance']);
    Route::post('/MarkAttendance', [AttendanceController::class, 'mark_attendance']);
    Route::post('/markoutattendance', [AttendanceController::class, 'markoutattendance']);
    Route::post('/checkloginattendance', [AttendanceController::class, 'checkLoginAttendance']);


    //leave module
    Route::post('/ApplyLeave', [LeaveController::class, 'apply_leave']);
    Route::post('/LeaveStatus', [LeaveController::class, 'leave_status']);
    // Route::post('/CancelLeave', [LeaveController::class, 'leave_cancel']);


    // for Test
    Route::post('/deleteAttendance', [AttendanceController::class, 'deleteAttendance']);
});


// v2 api with using status code response

Route::group(['middleware' => []], function () {

    // trip
    // Route::post('/getTodayTrips',[TripController::class,'getTodayTrip']);
    Route::post('/getUpcomingTrips', [TripController::class, 'getUpcomingTrips']);
    Route::post('/startTrip', [TripController::class, 'startTrip']);
    Route::post('/endTrip', [TripController::class, 'endTrip']);
    Route::post('/startUnloading', [TripController::class, 'startUnloading']);
    Route::post('/stopUnloading', [TripController::class, 'stopUnloading']);

    Route::post('/checkoutItems', [TripController::class, 'checkoutItems']);
    Route::post('/tripHistory', [TripController::class, 'tripHistories']);

    Route::post('/showTrip', [TripController::class, 'showTrip']);
    Route::post('/tripDetails', [TripController::class, 'tripDetails']);
    Route::get('/getDocuments', [TripController::class, 'getActiveDocuments']);
    Route::post('/getCheckedDocuments', [TripController::class, 'getCheckedDocuments']);

    // Expenses
    Route::get('/getExpenses', [ExpenseController::class, 'getActiveExpenses']);
    Route::post('/addExpense', [ExpenseController::class, 'addExpense']);


    Route::post('/getActiveTrips', [TripController::class, 'getActiveTrips']);
    Route::post('/showStartedTrip', [TripController::class, 'showStartedTrip']);

    Route::post('/getFuelData', [FuelController::class, 'getFuelData']);
    Route::post('/storeFuelData', [FuelController::class, 'storeFuelData']);
    Route::post('/getAssignedVehicles', [FuelController::class, 'getAssignedVehicles']);
    Route::get('/getFuelStations', [FuelController::class, 'getFuelStation']);


    Route::get('/getUoms', function () {
        $data = Uom::where('is_active', true)->get();
        if ($data->count() === 0) {
            return ApiResponse::noDataFound($data, 'Sorry, no active Units of Measurement are available at the moment.');
        }
        return ApiResponse::success($data);
    });
    Route::group(['prefix' => '/task-management'], function () {
        Route::post('/getTasksStat', [TaskManagementController::class, 'getTasksStat']);
        Route::post('/getTasks', [TaskManagementController::class, 'getTasks']);
        Route::post('/startTask', [TaskManagementController::class, 'startTask']);
        Route::post('/updateTask', [TaskManagementController::class, 'updateTask']);
        Route::post('/getTaskLog', [TaskManagementController::class, 'getTaskLog']);
        Route::post('/endTask', [TaskManagementController::class, 'endTask']);



    });

    Route::group(['prefix' => '/project-management'], function () {
        Route::post('/getProjects', [ProjectManagementController::class, 'getProjects']);
        Route::post('/getJobsByProjectId', [ProjectManagementController::class, 'getJobsByProjectId']);

        Route::post('/getJobDetails', [ProjectManagementController::class, 'getJobDetails']);
       // Route::post('/getTasks', [ProjectManagementController::class, 'getSubTasks']);

        Route::post('/startTask', [ProjectManagementController::class, 'startSubTask']);

        Route::post('/getInventoriesProducts', [ProjectManagementController::class, 'getInventoriesItems']);
       
        Route::post('/createRequest', [ProjectManagementController::class, 'createRequest']);
        Route::post('/getAllRequests', [ProjectManagementController::class, 'getAllRequests']);
        Route::post('/updateRequest', [ProjectManagementController::class, 'updateRequest']);


        Route::get('/getProducts', [ProjectManagementController::class, 'getProducts']);
        Route::get('/getVendors', [ProjectManagementController::class, 'getVendors']);
        Route::get('/getVehicles', [ProjectManagementController::class, 'getVehicles']);

        Route::post('/getSiteProducts', [ProgressReportController::class, 'getSiteProducts']);
        Route::post('/saveProgressReport', [ProgressReportController::class, 'storeProgressReport']);
        Route::post('/getProgress', [ProgressReportController::class, 'getProgress']);

        Route::post('/endTask', [ProjectManagementController::class, 'endSubTask']);

        // consignment and inventory
        Route::post('/getConsignments', [InventoryAndConsignmentController::class, 'getConsignments']);
        Route::post('/checkoutConsignments', [InventoryAndConsignmentController::class, 'checkoutConsignments']);

    });
    Route::group(['prefix' => '/inventory'], function () {
        Route::post('/getInventoryProducts', [WarehouseHeadAndDepotHeadInventoryAndConsignmentController::class, 'getProducts']);
        Route::post('/getProductDetails', [WarehouseHeadAndDepotHeadInventoryAndConsignmentController::class, 'getProductDetails']);
       
        Route::post('/getConsignments', [WarehouseHeadAndDepotHeadInventoryAndConsignmentController::class, 'getConsignments']);
        Route::post('/getConsignmentDetails', [WarehouseHeadAndDepotHeadInventoryAndConsignmentController::class, 'getConsignmentDetails']);

        Route::post('/checkoutConsignments', [WarehouseHeadAndDepotHeadInventoryAndConsignmentController::class, 'checkoutConsignments']);
    });
});
