<?php

namespace App\Http\Controllers\warehouseDepo;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\CheckoutConsignement;
use App\Models\CheckoutProducts;
use App\Models\Companie;
use App\Models\Consignement;
use App\Models\Consignment;
use App\Models\Depo;
use App\Models\Inventory;
use App\Models\InventroyHistory;
use App\Models\ProductMaster;
use App\Models\Products;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WhDpMapedUser;
use App\Notifications\SendPushNotification;
use App\Notifications\WebNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mappedWarehouseDepos = getMappedUserData();
        if ($mappedWarehouseDepos->isEmpty()) {
            return redirect()->back()->with('error', 'Oops! It seems you haven\'t been assigned to a warehouse and depot.');
        }
        // Retrieve filter parameters from the query string
        $productsNames = request()->input('productsNames');
        $companies = request()->input('companies');
        $warehouseDepots = request()->input('source_ids');

        $categoryIds = request()->input('category_id');

        $mappedwarehouseIds = $mappedWarehouseDepos->pluck('warehouse_id')->filter();
        $mappeddepotIds = $mappedWarehouseDepos->pluck('depo_id')->filter();

        $warehouseDepot = array_merge($mappedwarehouseIds->toArray(), $mappeddepotIds->toArray());
        $query = Inventory::orderByDesc('id');

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
        // return $warehouseDepot;
        $query->whereIn('source_id', $warehouseDepot);
        // Execute the query
        $products = $query->get()->groupBy('product_id');
        $allproducts = Inventory::get()->map(function ($inventory) {
            $inventory->source();
            return $inventory;
        });

        // Retrieve other related data based on filtered product data
        $categories = Categories::whereIn('id', $allproducts->pluck('product.category_id')->unique())->get();
        $productsIds = $allproducts->pluck('product_id')->filter()->unique()->toArray();
        $brandsIds = $allproducts->pluck('product.company_id')->filter()->unique()->toArray();

        $productData = ProductMaster::whereIn('id', $productsIds)->get();
        $companies = Companie::whereIn('id', $brandsIds)->get();

        $warehouses = Inventory::where('inventory_type_id', getInventoryTypeBySlug('warehouse'))
            ->whereIn('source_id', $mappedwarehouseIds)
            ->get()
            ->groupBy('source_id')
            ->map(function ($warehouse) {
                return (object)[
                    'id' => $warehouse->first()->source_id,
                    'inventory_type_id' => $warehouse->first()->inventory_type_id,
                    'name' => $warehouse->first()->source()->name,
                ];
            })->values();
        $depots = Inventory::where('inventory_type_id', getInventoryTypeBySlug('depot'))
            ->whereIn('source_id', $mappeddepotIds)
            ->get()
            ->groupBy('source_id')
            ->map(function ($depot) {
                return (object)[
                    'id' => $depot->first()->source_id,
                    'inventory_type_id' => $depot->first()->inventory_type_id,
                    'name' => $depot->first()->source()->name,
                ];
            })->values();
        $warehousesAndDepos = array_merge($warehouses->toArray(), $depots->toArray());

        $data = [
            'products' => $products,
            'categories' => $categories,
            'productData' => $productData,
            'companies' => $companies,
            'warehousesAndDepos' => $warehousesAndDepos

        ];
        return view('warehouse_depot.inventory', $data);
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
            // Validate the request data
            $request->validate([
                'consignement_id' => 'required|numeric',
                'missingDamageQty' => 'required|array',
                'actualQty' => 'required|array',
                'missingDamageQty.*.*' => 'required|numeric|min:0',
                'actualQty.*.*' => 'required|numeric|min:0',
            ]);

            // Get authenticated user and current timestamp
            $user = Auth::user();
            $now = now();

            // Find the consignment
            $consignment = Consignment::find($request->input('consignement_id'));

            if (!$consignment) {
                return redirect()->back()->with('error', 'Consignment not found');
            }

            // Use a database transaction
            DB::beginTransaction();

            try {
                // Create or update CheckoutConsignement
                $checkoutConsignement = CheckoutConsignement::updateOrCreate(
                    ['consignment_id' => $request->input('consignement_id')],
                    [
                        'user_id' => $user->id,
                        'origin_source_type_id' => $consignment->origin_source_type_id,
                        'origin_source_id' => $consignment->origin_source_id,
                        'destination_source_type_id' => $consignment->destination_source_type_id,
                        'destination_source_id' => $consignment->destination_source_id,
                        'date' => $now,
                    ]
                );

                // Save missingDamageQty and actualQty using checkoutProducts relationship
                $this->saveProducts($checkoutConsignement, $request->input('actualQty'), $request->input('missingDamageQty'), $request->input('description'));
                $this->addInventory($checkoutConsignement);

                // Send notification to admin
                $title = 'Consignment Checkout Accepted';
                $message = 'A consignment checkout has been accepted.';
                $admin = User::whereNull('role_id')->first(); // Assuming the admin is the authenticated user
                $data = [
                    'notification_type' => 'project_management',
                    'title' => $title,
                    'message' => $message,
                ];

                // Create and send the notification
                $notification = new SendPushNotification($title, $message, $admin, $data);
                $admin->notify($notification);


                // web notification
                $admin->notify(new WebNotification(route('admin.consignments.index'), $title, $message));

                // Commit the transaction
                DB::commit();

                // Redirect with success message
                return redirect()->back()->with('success', 'Data saved successfully');
            } catch (\Exception $e) {
                // An error occurred, rollback the transaction
                DB::rollBack();
                return $e;

                // Log or handle the error as needed
                Log::error($e);

                return redirect()->back()->with('error', 'An error occurred while saving the data');
            }
        } catch (ValidationException $e) {
            // Validation failed, return validation errors
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Handle other exceptions if needed

            // Log or report the exception
            Log::error($e);

            return redirect()->back()->with('error', 'An error occurred');
        }
    }

    private function saveProducts($consignment, $actualQty, $missingDamageQty, $description)
    {
        foreach ($actualQty as $productId => $quantity) {
            $product = ProductMaster::find($productId);

            if ($product) {
                $is_extra_missing = $missingDamageQty[$productId][0] > 0 ? true : false;
                $consignment->checkout_products()->updateOrCreate(
                    ['product_id' => $product->id],
                    [
                        'actual_quantity' => $quantity[0],
                        'missing_damage_quantity' => $missingDamageQty[$productId][0],
                        'is_extra_missing' => $is_extra_missing,
                        'description' =>  $is_extra_missing ? $description[$productId][0] : null,
                    ]
                );
            }
        }
    }

    private function addInventory($checkoutConsignement)
    {
        if ($checkoutConsignement->consignment) {
            foreach ($checkoutConsignement->checkout_products as $checkoutProduct) {
                $product = Inventory::where([
                    'product_id' => $checkoutProduct->product_id,
                    'inventory_type_id' => $checkoutConsignement->consignment->origin_source_type_id,
                    'source_id' =>  $checkoutConsignement->consignment->origin_source_id,
                ])->first();

                if ($product) {
                    // Calculate new quantity
                    $quantity = intval($product->quantity);
                    $transferQuantity = intval($checkoutProduct->actual_quantity);
                    $newQuantity = $quantity - $transferQuantity;

                    // Update quantity in the origin
                    $product->update(['quantity' => $newQuantity]);

                    $admin = User::whereNull('role_id')->first();
                    // Create history for the transfer
                    $this->createInventoryHistory(
                        $product,
                        $checkoutConsignement->consignment->origin_source_type_id,
                        $checkoutConsignement->consignment->origin_source_id,
                        $checkoutConsignement->consignment->destination_source_type_id,
                        $checkoutConsignement->consignment->destination_source_id,
                        $transferQuantity,
                        'Transferred',
                        $admin,
                        // add here to date if we needed to show trip creation date
                    );
                    // Update or create destination product
                    $this->updateOrCreateDestinationProduct(
                        $checkoutProduct->product_id,
                        $checkoutConsignement->consignment->destination_source_type_id,
                        $checkoutConsignement->consignment->destination_source_id,
                        $transferQuantity
                    );

                    // Create history for the receiving at the destination
                    $this->createInventoryHistory(
                        $product,
                        $checkoutConsignement->consignment->destination_source_type_id,
                        $checkoutConsignement->consignment->destination_source_id,
                        $checkoutConsignement->consignment->origin_source_type_id,
                        $checkoutConsignement->consignment->origin_source_id,
                        $transferQuantity,
                        'Received'
                    );
                    checkMinStockLevelOrSendNotification($product->product_id,$product->source_id,$product->inventory_type_id);
                }
            }
        }
    }

    private function createInventoryHistory(
        $product,
        $inventoryTypeId,
        $sourceId,
        $transferInventoryTypeId,
        $transferSourceId,
        $quantity,
        $action,
        $user = null,
        $createdAt = null,
        $updatedAt = null
    ) {
        $user = $user ?? Auth::user(); // Use provided user or fallback to authenticated user

        $data = [
            'product_id' => $product->product_id,
            'inventory_type_id' => $inventoryTypeId,
            'source_id' => $sourceId,
            'tr_inventory_type_id' => $transferInventoryTypeId,
            'tr_source_id' => $transferSourceId,
            'quantity' => $quantity,
            'user_id' => $user->id,
            'action' => $action,
            'description' => 'Product ' . $action . ' in inventory by ' . $user->name,
        ];

        if ($createdAt !== null) {
            $data['created_at'] = $createdAt;
        }

        if ($updatedAt !== null) {
            $data['updated_at'] = $updatedAt;
        }

        return InventroyHistory::create($data);
    }
    private function updateOrCreateDestinationProduct($productId, $inventoryTypeId, $sourceId, $transferQuantity)
    {
        $destinationProduct = Inventory::where([
            'product_id' => $productId,
            'inventory_type_id' => $inventoryTypeId,
            'source_id' => $sourceId,
        ])->first();

        if ($destinationProduct) {
            // Product exists in the destination, update its quantity
            $transferQuantity += intval($destinationProduct->quantity);
            $destinationProduct->update([
                'quantity' => $transferQuantity,
                'available_quantity' => $transferQuantity,
            ]);
        } else {
            // Product doesn't exist in the destination, create a new entry
            Inventory::create([
                'product_id' => $productId,
                'inventory_type_id' => $inventoryTypeId,
                'source_id' => $sourceId,
                'quantity' => $transferQuantity,
                'available_quantity' => $transferQuantity,
            ]);
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
        // $data = Inventory::with(['productWithCategory', 'company', 'warehouse', 'depo', 'vendor'])->find($id);
        // return response()->json(['status' => 200, 'data' => $data]);
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
        //
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
    public function viewDetails($id)
    {
        $mappedWarehouseDepos = getMappedUserData();
        if ($mappedWarehouseDepos->isEmpty()) {
            return redirect()->back()->with('error', 'Oops! It seems you haven\'t been assigned to a warehouse and depot.');
        }
        $mappedwarehouseIds = $mappedWarehouseDepos->pluck('warehouse_id')->filter();
        $mappeddepotIds = $mappedWarehouseDepos->pluck('depo_id')->filter();

        $warehouseDepot = array_merge($mappedwarehouseIds->toArray(), $mappeddepotIds->toArray());

        $data = Inventory::whereIn('source_id', $warehouseDepot)->where('product_id', $id)->get();
        $history = InventroyHistory::where('product_id', $id)->whereIn('source_id', $warehouseDepot)->orderByDesc('created_at') ->paginate(25);
        return view('warehouse_depot.inventory_details', compact('data', 'history'));
    }
}
