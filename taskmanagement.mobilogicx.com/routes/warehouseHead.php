<?php

use App\Http\Controllers\FuleController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\warehouseDepo\InventoryController;
use App\Http\Controllers\warehouseDepo\MatrialConfirmationController;
use App\Http\Controllers\warehouseDepo\VehicleController;
use Illuminate\Support\Facades\Route;






Route::group(['prefix' => '/warehouse-head', 'as' => 'whHead.', 'middleware' => ['auth','warehouse_head']], function () {
    Route::get('/dashboard',function(){
        return view('warehouse_head.dashboard');
    })->name('dashboard');

    Route::resource('/fule-management', FuleController::class);
    
    Route::get('/assigned-vehicles',[VehicleController::class,'index'])->name('vehicles.index');

    Route::resource('/inventory-management', InventoryController::class);
    Route::get('/inventory/details/{pro_id}', [InventoryController::class,'viewDetails'])->name('inventory.viewDetails');

    
    Route::resource('/consignements', MatrialConfirmationController::class);
    Route::get('/consignements-checkout/{con_id}', [MatrialConfirmationController::class,'checkout'])->name('conCheckout');
    Route::get('/get-challan/{id}', [TripController::class, 'getChallan'])->name('getChallan');
    Route::get('/get-bill/{id}', [TripController::class, 'getBill'])->name('getBill');
    Route::get('/get-expense/{id}', [TripController::class, 'getExpense'])->name('getExpense');
});

