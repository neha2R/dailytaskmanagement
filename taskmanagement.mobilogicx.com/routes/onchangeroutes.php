<?php

use App\Http\Controllers\consignment\StockTransferController;
use App\Models\ProductMaster;
use App\Models\Products;
use Illuminate\Support\Facades\Route;

Route::get('/getModels/{id}',function($id){
    return getVehiclesModels($id);
})->name('getModels');

Route::get('/getSubCategory/{id}',function($id){
    return getSubCategory($id);
})->name('getSubCategory');

Route::get('/getProduct/{id}',function($id){
    return ProductMaster::with(['category','sub_category'])->find($id);
});


// Route::get('/getdProducts/{id}',[StockTransferController::class,'getAvailebleProducts']);
// Route::get('/getdProducts/{id}',function($id){
//     return Products::where('warehouse_id',$id)->with('productWithCategory')->get()->map(function ($product) {
//         // Calculate max_val for each product and add it to the object
//         $product->availeble_stock = $this->calculateMaxVal($product);
//         return $product;
//     });   
//     function calculateMaxVal($product) {
//         $consignements=Consignement::where(['warehouse_from_id'=>$product->warehouse_id,'status'=>'pending'])->with('products')->get();
//     }
// }
// )->name('getSubCategory');
