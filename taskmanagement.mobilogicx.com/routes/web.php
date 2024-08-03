<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\consignment\ConsignmentController;
use App\Http\Controllers\Departmentcontroller;
use App\Http\Controllers\DepoController;
use App\Http\Controllers\DivisionSiteController;
use App\Http\Controllers\DpWhMapController;
use App\Http\Controllers\DriverMapController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FuleController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\MonthlyAttendanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProductMasterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\VehicleDepoWarehouseController;
use App\Http\Controllers\VehicleDocumentController;
use App\Http\Controllers\VehicleMasterController;
use App\Http\Controllers\VehiclesController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\WarehouseAndDepoController;
use App\Http\Controllers\WarehouseController;
use App\Models\Division;
use App\Models\User;
use App\Notifications\AndroidNotification;
use App\Notifications\TripCreateNotification;
use App\Notifications\WebNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('privacy-policy', function () {
    return view('privacy-policy');
})->name('privacy-policy');


Route::get('/run', function () {
    //Artisan::call("config:cache");
    //Artisan::call("config:clear");
    //Artisan::call("cache:clear");
    //Artisan::call("view:clear");
    Artisan::call("overdue:reminders");
    
    
    
});
require __DIR__ . '/auth.php';
require __DIR__ . '/onchangeroutes.php';
require __DIR__ . '/warehouseHead.php';
require __DIR__ . '/depotHead.php';
require __DIR__ . '/projectManagement.php';

// profile route for use all users
Route::group(['prefix' => '/update-account-details', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    Route::resource('/profile', ProfileController::class);
    Route::patch('/store-token', [NotificationController::class, 'updateDeviceToken'])->name('store.token');

    Route::get('/fetch-notifications', [NotificationController::class, 'fetchNotifications']);
    Route::post('/clear-notifications', [NotificationController::class, 'clearNotifications']);
});
Route::get('/home', function () {
    return view('guest.dashboard');
})->name('guest-home');



Route::group(['prefix' => '/admin', 'as' => 'admin.', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/dashboard', [HomeController::class,'AdminHome'])->name('dashboard');

    Route::resource('/department', Departmentcontroller::class);
    Route::post('/department/status', [Departmentcontroller::class, 'change_status'])->name('dep.status');

    Route::resource('/roles', RolesController::class);
    Route::post('/roles/status', [RolesController::class, 'change_status'])->name('role.status');

    Route::resource('/position', PositionController::class);
    Route::post('/position/status', [PositionController::class, 'change_status'])->name('pos.status');

    Route::resource('/employees', EmployeeController::class);
    Route::post('/employees/status', [EmployeeController::class, 'change_status'])->name('emp.status');

    Route::resource('/attendance', AttendanceController::class);
    Route::get('/attendance/{id}/{active_date}', [AttendanceController::class, 'showEmp'])->name('emp.show');
    Route::resource('/monthly_attendance', MonthlyAttendanceController::class);
    Route::get('/monthly_attendance/{id}/{active_date}', [MonthlyAttendanceController::class, 'showMonhlyAttendance'])->name('emp.show');

    Route::resource('/leaves', LeaveController::class);
    Route::resource('/assignement', AssignmentController::class);
    Route::get('/get_users/{id}', [TaskController::class, 'getUsers']);
    Route::get('/get_roles/{id}', [EmployeeController::class, 'getRoles']);

    Route::get('/get_seniors/{id}', [AssignmentController::class, 'getSeniors']);
    Route::get('/show_assignement/{id}', [AssignmentController::class, 'showAssignement']);
    Route::get('/remove_assignement/{id}', [AssignmentController::class, 'destroy']);

    // inv manegement
    Route::resource('/category', CategoryController::class);
    Route::post('/category/status', [CategoryController::class, 'change_status'])->name('category.status');
    Route::get('/show_sub_category/{id}', [CategoryController::class, 'showSubCategory']);

    Route::resource('/companies', CompaniesController::class);
    Route::post('/companies/status', [CompaniesController::class, 'change_status'])->name('companies.status');

    Route::resource('/vendor', VendorController::class);
    Route::post('/vendor/status', [VendorController::class, 'change_status'])->name('vendor.status');
    Route::post('/vendor/bulkUpload', [VendorController::class, 'bulkUpload'])->name('vendor.bulkUpload');

    Route::resource('/warehouse', WarehouseController::class);
    Route::post('/warehouse/status', [WarehouseController::class, 'change_status'])->name('warehouse.status');

    Route::resource('/depo', DepoController::class);
    Route::post('/depo/status', [DepoController::class, 'change_status'])->name('depo.status');

    Route::resource('/productMaster', ProductMasterController::class);
    Route::post('/productMaster/status', [ProductMasterController::class, 'change_status'])->name('productMaster.status');
    Route::post('/checkUniqueProductName', [ProductMasterController::class, 'checkUniqueProductName'])
        ->name('checkUniqueProductName');


    Route::resource('/inventory', InventoryController::class);
    Route::get('/inventory/details/{pro_id}', [InventoryController::class, 'viewDetails'])->name('inventory.viewDetails');

    Route::resource('/consignments', ConsignmentController::class);
    Route::get('get-cons-locations/{type}', [ConsignmentController::class, 'getLocations'])->name('getLocations');
    Route::get('/getProducts/{id}/{type}', [ConsignmentController::class, 'getAvailableProducts'])->name('getProducts');
    Route::get('/getProductDetails/{id}', [ConsignmentController::class, 'getProductDetails'])->name('getProductDetails');

    // site and divisions
    Route::resource('/divisions-sites', DivisionSiteController::class);
    Route::get('/divisions-subdivisions', [DivisionSiteController::class, 'subDivision'])->name('subDivisions');
    Route::get('/divisions-site', [DivisionSiteController::class, 'site'])->name('sites');

    Route::post('/divisions-sites/sub-div-store', [DivisionSiteController::class, 'subDivStore'])->name('subDivStore');
    Route::post('/divisions-sites/site-store', [DivisionSiteController::class, 'siteStore'])->name('siteStore');

    // site div status
    Route::post('/divisions-sites/div-status', [DivisionSiteController::class, 'divStatus'])->name('divStatus');
    Route::post('/divisions-sites/sub-div-status', [DivisionSiteController::class, 'subDivStatus'])->name('subDivStatus');
    Route::post('/divisions-sites/site-status', [DivisionSiteController::class, 'siteStatus'])->name('siteStatus');

    // edit
    Route::get('/divisions-sites/sub-div-edit/{id}', [DivisionSiteController::class, 'subDivEdit'])->name('subDivEdit');
    Route::get('/divisions-sites/site-edit/{id}', [DivisionSiteController::class, 'siteEdit'])->name('siteEdit');

    // update data
    Route::post('/divisions-sites/sub-div-update', [DivisionSiteController::class, 'subDivUpdate'])->name('subDivUpdate');
    Route::post('/divisions-sites/site-update', [DivisionSiteController::class, 'siteUpdate'])->name('siteUpdate');
    Route::post('/divisions-sites/maping', [DivisionSiteController::class, 'maping'])->name('divisionSiteMaping');
    Route::get('/divisions-sites-history/{id}/{type}', [DivisionSiteController::class, 'mappingHistory'])->name('divisionSiteHistory');
    Route::get('/divisions-sites-unmap/{id}/{type}', [DivisionSiteController::class, 'unmap'])->name('divisionSiteUnmap');

    // warehouse and depo map
    Route::post('/warehouse-depo-map', [DpWhMapController::class, 'store'])->name('warehouseDepoMap');
    Route::post('/warehouse-depo-multiMap', [DpWhMapController::class, 'multiMapping'])->name('warehouseDepoMultiMap');
    Route::get('/warehouse-depo-history/{id}/{type}', [DpWhMapController::class, 'mapingHistory'])->name('whDpHistoryMapUnmap');
    Route::get('/warehouse-depo-unmap/{id}/{type}', [DpWhMapController::class, 'unmap'])->name('warehouseDepoUnmap');

    Route::resource('/fule-management', FuleController::class);
    Route::resource('/manage-tasks', TaskController::class);
    Route::post('/task/status', [TaskController::class, 'change_status'])->name('task.status');
    Route::get('/manage-tasks/prioritylevel/{id}', [TaskController::class, 'prioritylevel'])->name('manage-tasks.prioritylevel');
    Route::get('/manage-tasks/show1/{id}', [TaskController::class, 'show1'])->name('manage-tasks.show1');


});

// vehicles
Route::group(['prefix' => '/admin', 'as' => 'vehicle.', 'middleware' => ['auth', 'admin']], function () {
    // Vehicle Master routes
    Route::resource('/vehicleMaster', VehicleMasterController::class);
    Route::post('/vehicleMaster/storeVehicleModel', [VehicleMasterController::class, 'storeVehicleModel'])->name('storeVehicleModel');
    Route::post('/vehicleMaster/updateVehicleModel', [VehicleMasterController::class, 'updateVehicleModel'])->name('updateVehicleModel');
    Route::get('/vehicleMaster/showVehicleModel/{id}', [VehicleMasterController::class, 'showVehicleModel'])->name('showVehicleModel');
    Route::post('/vehicleMaster/changeVMFstatus', [VehicleMasterController::class, 'changeVMFstatus'])->name('changeVMFstatus');
    Route::post('/vehicleMaster/changeVMstatus', [VehicleMasterController::class, 'changeVMstatus'])->name('changeVMstatus');

    Route::resource('/vehicles', VehiclesController::class);
    Route::post('/vehicles/status', [VehiclesController::class, 'change_status'])->name('vehicles.status');
    Route::get('/vehicles/vehicle-details/{id}', [VehiclesController::class, 'vehicleDetails'])->name('vehicleDetails');
    // service sechduling
    Route::post('/vehicles/store-service-details', [VehiclesController::class, 'storeServiceDetails'])->name('vehicles.storeServiceDetails');
    Route::get('/get-service-details/{id}', [VehiclesController::class, 'getServiceDetails'])->name('serviceDetails');
    Route::post('/checkUniqueVehicleNumber', [VehiclesController::class, 'checkUniqueVehicleNumber'])
        ->name('checkUniqueVehicleNumber');
    // driver maping
    Route::resource('/driver', DriverMapController::class);
    Route::get('driver/mapingHistory/{id}', [DriverMapController::class, 'mapingHistory'])->name('historyMapUnmap');

    // assign vehicle in warehouse and depo
    Route::resource('/map-warehouse-dep', VehicleDepoWarehouseController::class);
    Route::get('warehouse-depo-vehicle-map/mapingHistory/{id}', [VehicleDepoWarehouseController::class, 'mapingHistory'])->name('historyMapUnmapWarehouseDepo');
    Route::post('warehouse-depo-vehicle-map/bulk-store', [VehicleDepoWarehouseController::class, 'bulk_store'])->name('bulk_store');

    Route::post('/vehicle-documents-store-insurance', [VehicleDocumentController::class, 'storeInsuranceDocument'])->name('storeInsurance');
    Route::post('/vehicle-documents-store-pucc', [VehicleDocumentController::class, 'storePUCCDocument'])->name('storePUCC');
    Route::post('/vehicle-documents-store-fitness', [VehicleDocumentController::class, 'storeFitnessDocument'])->name('storeFitness');
    Route::post('/vehicle-documents-store-tax', [VehicleDocumentController::class, 'storeTaxDocument'])->name('storeTax');
    Route::post('/vehicle-documents-store-national-permit', [VehicleDocumentController::class, 'storeNationalPermitDocument'])->name('storeNationalPermit');
    Route::post('/vehicle-documents-store-state-permit', [VehicleDocumentController::class, 'storeStatePermitDocument'])->name('storeStatePermit');

    Route::get('/vehicle-documents-show/{id}', [VehicleDocumentController::class, 'showVehicleDocuments'])->name('showVehicleDocument');
});



Route::group(['prefix' => '/admin', 'as' => 'trips.', 'middleware' => ['auth', 'admin']], function () {
    // Trip routes
    Route::resource('/trip', TripController::class);
    Route::get('getConsignements/{id}/{type}', [TripController::class, 'getConsignments'])->name('getConsignments');
    Route::get('get-locations/{type}', [TripController::class, 'getLocations'])->name('getLocations');
    Route::get('get-consignment-details/{id}', [TripController::class, 'getConDetails'])->name('getConDetails');


    Route::get('/trip/getDriver/{id}', [TripController::class, 'getDriver'])->name('getDriver');
    Route::post('/getVehicles', [TripController::class, 'getVehicles'])->name('getVehicles');

    Route::post('/store-expense', [TripController::class, 'storeExpenseDetails'])->name('storeExpenseDetails');
    Route::post('/store-challan', [TripController::class, 'storeChallanDetails'])->name('storeChallanDetails');
    Route::post('/store-bill', [TripController::class, 'storeBillDetails'])->name('storeBillDetails');

    // view data
    Route::get('/tripView-getAllConsignements/{trip_id}', [TripController::class, 'getAllCons'])->name('getAllCons');
    Route::get('/tripView-getConsignements/{trip_id}/{loc_id}', [TripController::class, 'getCons'])->name('getCons');

    Route::get('/get-challan/{id}', [TripController::class, 'getChallan'])->name('getChallan');
    Route::get('/get-bill/{id}', [TripController::class, 'getBill'])->name('getBill');
    Route::get('/get-expense/{id}', [TripController::class, 'getExpense'])->name('getExpense');
});

// use Illuminate\Support\Str;

Route::get('/update-password', function () {
    $users = User::where('role_id', '!=', null)->get();
    foreach ($users as $key => $value) {
        // return $value;
        $value->update(['password' => Hash::make($value->mobile)]);
    }
    return $users;
});
Route::get('/send-notification', function () {

    $user = User::find(1);
    // Send the TripCreateNotification
    $title = 'New Trip Created';
    $message = 'A new trip has been created.';
    $data = [
        'title'=>$title,
        'message'=>$message,
        'notification_type' => "trip",
    ];

    // Create and send the notification
    $notification = new AndroidNotification($user, $data);
    $notify=$user->notify($notification);

    dd($user->notify($notification));

    // $result = sendNotification('Notification Title', 'Notification Body', 1);
    // $user = User::find(1);
    // $result1 =  $user->notify(new WebNotification(route('login'), 'Consignment Delivered', 'trip has been delivered by Mahaveer'));
    // return $result;
});
