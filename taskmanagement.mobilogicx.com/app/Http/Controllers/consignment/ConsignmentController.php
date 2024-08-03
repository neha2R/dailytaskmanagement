<?php

namespace App\Http\Controllers\consignment;

use App\Http\Controllers\Controller;
use App\Models\ConsignementProducts;
use App\Models\Consignment;
use App\Models\Depo;
use App\Models\Inventory;
use App\Models\InventoryType;
use App\Models\ProductMaster;
use App\Models\Products;
use App\Models\Project;
use App\Models\Site;
use App\Models\SiteInventory;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ConsignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Consignment::withCount('products')->orderBy('created_at', 'desc')->get();
        return view('admin.consignment.consignment', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $locationTypes = InventoryType::where('is_active', true)->get();
        $activeProjects = Project::where('status', 'in-process')->get();

        return view('admin.consignment.create_con', compact('locationTypes', 'activeProjects'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $siteTypeId = getInventoryTypeBySlug('site');
            // Validate the request data
            $validator = Validator::make($request->all(), [
                '_token' => 'required|string',
                'origin_location_type' => 'required',
                'delivery_location_type' => 'required',
                'origin_location' => 'required',
                'delivery_location' => 'required',
                'delivery_by_date' => 'required',
                'products' => 'required|array',
                'products.*' => 'required|numeric',
                'transfer_qty' => 'required|array',
                'transfer_qty.*' => 'required|numeric',
                'project_id' => 'required_if:delivery_location_type,' . $siteTypeId . '',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $errorMessages = $this->checkInventory($request);

            if (!empty($errorMessages)) {
                return response()->json(['errors' => $errorMessages], 422);
            }

            // Consignment data
            $consignmentData = [
                'origin_source_type_id' => $request->input('origin_location_type'),
                'origin_source_id' => $request->input('origin_location'),
                'destination_source_type_id' => $request->input('delivery_location_type'),
                'destination_source_id' => $request->input('delivery_location'),
                'delivery_by_date' => dateformat($request->input('delivery_by_date'), 'Y-m-d'),
                'project_id' => $request->project_id,
            ];

            // Create Consignment
            $create = Consignment::create($consignmentData);

            // Process products
            foreach ($request->products as $key => $value) {
                // Get product information based on origin location type
                $productInfo = $this->getProductInfo($request, $value);

                if ($productInfo) {
                    // Update available quantity and create ConsignmentProducts record
                    $this->updateProductInfo($productInfo, $request->transfer_qty[$key], $create->id, $value);
                }
            }

            // Successful response
            return response()->json(['status' => 200, 'message' => 'Consignment created successfully']);
        } catch (\Exception $e) {
            // Log the exception or handle it as needed
            return response()->json(['error' => 'An error occurred while creating the consignment. ' . $e], 500);
        }
    }

    private function checkInventory($request)
    {
        $errorMessages = [];

        $warehouseTypeId = getInventoryTypeBySlug('warehouse');
        $depotTypeId = getInventoryTypeBySlug('depot');
        $siteTypeId = getInventoryTypeBySlug('site');

        foreach ($request->products as $key => $productId) {
            $requestedQuantity = $request->transfer_qty[$key];

            if ($request->origin_location_type == $warehouseTypeId || $request->origin_location_type == $depotTypeId) {
                $productInfo = Inventory::with('product')
                    ->where('source_id', $request->origin_location)
                    ->where('inventory_type_id', $request->origin_location_type)
                    ->where('product_id', $productId)
                    ->first();
            } elseif ($request->origin_location_type == $siteTypeId) {
                $productInfo = SiteInventory::where('site_id', $request->origin_location)
                    ->where('product_id', $productId)
                    ->first();
            }

            if (!$productInfo) {
                $errorMessages[] = "Product with ID $productId not found in the inventory. Please check the product ID and try again.";
            } elseif ($productInfo->available_quantity < $requestedQuantity) {
                $errorMessages[] = "Insufficient stock for {$productInfo->product->name}. The available quantity is {$productInfo->available_quantity} {$productInfo->product->uom->name} for the consignment creation.";
            }
        }

        return $errorMessages;
    }


    // Helper function to get product information based on origin location type
    private function getProductInfo($request, $value)
    {
        $warehouseTypeId = getInventoryTypeBySlug('warehouse');
        $depotTypeId = getInventoryTypeBySlug('depot');
        $siteTypeId = getInventoryTypeBySlug('site');

        if ($request->input('origin_location_type') == $warehouseTypeId || $request->input('origin_location_type') == $depotTypeId) {
            return Inventory::with('product')
                ->where('source_id',  $request->input('origin_location'))
                ->where('inventory_type_id', $request->input('origin_location_type'))
                ->where('product_id', $value)
                ->first();
        } elseif ($request->input('origin_location_type') == $siteTypeId) {
            return SiteInventory::where('site_id',  $request->input('origin_location'))
                ->where('product_id', $value)
                ->first();
        }

        return null;
    }

    // Helper function to update product information and create ConsignmentProducts record
    private function updateProductInfo($productInfo, $quantity, $consignmentId, $productId)
    {
        $productInfo->decrement('available_quantity', $quantity);

        ConsignementProducts::create([
            'consignment_id' => $consignmentId,
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Consignment::find($id);
        $data->con_num = env('PrefixCon') . $data->id;
        $data->delivery_by_date = dateformat($data->delivery_by_date, 'd M Y');
        $data->origin_location = $data->origin_source()->name;
        $data->destination_location = $data->destination_source()->name;

        $products = $data->products->map(function ($product) {
            return [
                'product_name' => $product->product->name ?? "",
                'category_name' => $product->product->category->name ?? "",
                'quantity' => $product->quantity ?? "",
                'uom_name' => $product->product->uom->name ?? "",
            ];
        });
        return response()->json(['status' => 200, 'con' => $data, 'products' => $products]);
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
        $request->validate([
            'id' => 'required'
        ]);
        // update old
        foreach ($request->oldproducts as $key => $value) {
            ConsignementProducts::find($key)->update([
                'product_id' => $value,
                'quantity' => $request->oldtransfer_qty[$key]
            ]);
        }
        $del = ConsignementProducts::where('consignement_id', $request->id)->whereNotIn('id', array_keys($request->oldproducts))->delete();
        if (!empty($request->products)) {
            // add new 
            foreach ($request->products as $key => $value) {
                ConsignementProducts::create([
                    'consignement_id' => $request->id,
                    'product_id' => $value,
                    'quantity' => $request->transfer_qty[$key],
                ]);
            }
        }
        return redirect()->back()->with('success', 'Updated successfully');
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
            $consignment = Consignment::with('products')->findOrFail($id);

            DB::transaction(function () use ($consignment) {
                foreach ($consignment->products as $productDetail) {
                    $inventory = Inventory::where([
                        'product_id' => $productDetail->product_id,
                        'inventory_type_id' => $consignment->origin_source_type_id,
                        'source_id' => $consignment->origin_source_id
                    ])->first();

                    if ($inventory) {
                        $inventory->increment('available_quantity', $productDetail->quantity);
                    }
                }

                $consignment->delete();
            });

            return response()->json(['status' => 200, 'message' => 'Consignment deleted successfully']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['status' => 404, 'message' => 'Consignment not found'], 404);
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error("Error deleting consignment: " . $e->getMessage());

            return response()->json(['status' => 500, 'message' => 'An error occurred while deleting the consignment'], 500);
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


    public function getAvailableProducts($id, $type)
    {
        $warehouseTypeId = getInventoryTypeBySlug('warehouse');
        $depotTypeId = getInventoryTypeBySlug('depot');
        $siteTypeId = getInventoryTypeBySlug('site');
        if ($type == $warehouseTypeId || $type == $depotTypeId) {
            $data = Inventory::where('inventory_type_id', $type)->where('source_id', $id)->get()->map(function ($product) {
                return [
                    'id' => $product->product_id,
                    'name' => $product->product->name,
                    'quantity' => $product->quantity,
                ];
            });
        } elseif ($type == $siteTypeId) {
            $data = SiteInventory::where('site_id', $id)->get()->map(function ($product) {
                return [
                    'id' => $product->product_id,
                    'name' => $product->product->name,
                    'quantity' => $product->available_quantity,
                ];
            });
        }
        return response()->json(['status' => 200, 'products' => $data]);
    }
    public function getProductDetails($id)
    {
        $product = ProductMaster::select('id', 'name', 'category_id', 'uom_id')->find($id);

        if (!$product) {
            return response()->json(['status' => 404, 'message' => 'Product not found'], 404);
        }

        // Assuming you have relationships defined in your ProductMaster model for category and uom
        $product->load('category', 'uom');

        // Add additional information to the product object
        $product->category_name = $product->category->name ?? null;
        $product->uom_name = $product->uom->name ?? null;

        return response()->json(['status' => 200, 'product' => $product]);
    }
}
