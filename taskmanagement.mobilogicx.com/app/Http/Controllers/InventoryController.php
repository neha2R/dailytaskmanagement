<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Companie;
use App\Models\Depo;
use App\Models\Inventory;
use App\Models\InventroyHistory;
use App\Models\ProductMaster;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieve filter parameters from the query string
        $productsNames = request()->input('productsNames');
        $companies = request()->input('companies');
        $warehouseDepots = request()->input('source_ids');

        $categoryIds = request()->input('category_id');

        // Initial query with relationships
        $query = Inventory::orderByDesc('id');

        // return $query->get();;

        // Apply filters
        if (!empty($productsNames)) {
            $query->whereIn('product_id', $productsNames);
        }

        if (!empty($companies)) {
            $query->whereHas('product', function ($query) use ($companies) {
                $query->whereIn('company_id', $companies);
            });
        }

        if (!empty($warehouseDepots)) {
            $decodedValues = array_map(function ($jsonString) {
                return json_decode($jsonString, true);
            }, $warehouseDepots);
        
            $ids = array_column($decodedValues, 'id');
            $inventoryTypeIds = array_column($decodedValues, 'inventory_type_id');
        
            $query->whereIn('source_id', $ids)
                  ->whereIn('inventory_type_id', $inventoryTypeIds);
        }
        

        if (!empty($categoryIds)) {
            $query->whereHas('product', function ($query) use ($categoryIds) {
                $query->whereIn('category_id', $categoryIds);
            });
        }

        // Execute the query
        $products = $query->get()->groupBy('product_id');
        // return $products;
        $allproducts = Inventory::get()->map(function ($inventory) {
            $inventory->source();
            return $inventory;
        });
        // return $allproducts;
        // Retrieve other related data based on filtered product data
        $categories = Categories::whereIn('id', $allproducts->pluck('product.category_id')->unique())->get();
        $productsIds = $allproducts->pluck('product_id')->filter()->unique()->toArray();
        $brandsIds = $allproducts->pluck('product.company_id')->filter()->unique()->toArray();

        $productData = ProductMaster::whereIn('id', $productsIds)->get();
        $companies = Companie::whereIn('id', $brandsIds)->get();

        $warehouses = Inventory::where('inventory_type_id', getInventoryTypeBySlug('warehouse'))
            ->get()
            ->groupBy('source_id')
            ->map(function ($warehouse) {
                return (object)[
                    'id'=>$warehouse->first()->source_id,
                    'inventory_type_id' => $warehouse->first()->inventory_type_id,
                    'name' => $warehouse->first()->source()->name,
                ];
            })->values();
        $depots = Inventory::where('inventory_type_id', getInventoryTypeBySlug('depot'))
            ->get()
            ->groupBy('source_id')
            ->map(function ($depot) {
                return (object)[
                    'id'=>$depot->first()->source_id,
                    'inventory_type_id' => $depot->first()->inventory_type_id,
                    'name' => $depot->first()->source()->name,
                ];
            })->values();
        $warehousesAndDepos = array_merge($warehouses->toArray(), $depots->toArray());

        return view('admin.inv_management.inventory', compact('products', 'categories', 'warehousesAndDepos', 'productData', 'companies'));
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
        // return $request;
        try {
            DB::beginTransaction();

            $request->validate([
                'product_id' => 'required',
                // 'price' => 'required|max:8',
                'vendor_id' => 'required',
                'product_quantity' => 'required',
            ]);

            $productData = [
                'product_id' => $request->product_id,
                // 'price' => $request->price,
            ];

            if ($request->warehouse_id) {
                $request->validate([
                    'warehouse_id' => 'required',
                ]);

                $productData['source_id'] = $request->warehouse_id;
                $productData['inventory_type_id'] = getInventoryTypeBySlug('warehouse');
            } elseif ($request->depo_id) {
                $request->validate([
                    'depo_id' => 'required',
                ]);

                $productData['source_id'] = $request->depo_id;
                $productData['inventory_type_id'] = getInventoryTypeBySlug('depot');
            } else {
                throw new \Exception('Invalid request: either warehouse or depo must be provided.');
            }
            if (!$productData['inventory_type_id']) {
                return redirect()->back()->with('error', 'Inventory type is not found please re add');
            }
            $product = Inventory::where($productData)->first();

            $quantity = $request->product_quantity;
            $availableQty = $request->product_quantity;

            if ($product) {

                $quantity += intval($product->quantity);
                $availableQty += intval($product->available_quantity);
            }

            $create = Inventory::updateOrCreate(
                $productData,
                [
                    'quantity' => $quantity,
                    'available_quantity' => $availableQty
                ]
            );

            // add data in history
            if ($create) {
                $action = 'Stocked';
                $history = InventroyHistory::create(array_merge($productData, [
                    'user_id' => Auth::user()->id,
                    'vendor_id' => $request->vendor_id,
                    'quantity' => $request->product_quantity,
                    'action' => $action,
                    'description' => 'Product added to inventory by ' . Auth::user()->name,
                ]));
            }
            DB::commit();

            return redirect()->back()->with('success', 'Product added successfully');
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            // Log the error or handle it as appropriate for your application
            return redirect()->back()->with('error', 'An error occurred while processing your request. Please try again.');
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
        $product = Inventory::find($id);

        $data = Inventory::with([$product->inventory_type, 'product.company', 'product.category', 'product.sub_category'])->find($id);


        return response()->json(['status' => 200, 'data' => $data]);
    }
    public function viewDetails($id)
    {
        $data = Inventory::where('product_id', $id)->get();
        $history =InventroyHistory::where('product_id', $id)
        ->orderBy('id', 'desc')
        ->paginate(25);

        return view('admin.inv_management.inventory_details', compact('data', 'history'));
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
}
