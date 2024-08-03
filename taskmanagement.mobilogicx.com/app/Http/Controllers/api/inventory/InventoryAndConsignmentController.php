<?php

namespace App\Http\Controllers\api\inventory;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\CheckoutConsignement;
use App\Models\Consignment;
use App\Models\Inventory;
use App\Models\InventroyHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InventoryAndConsignmentController extends Controller
{
    public function getProducts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->first());
        }

        // Retrieve the user
        $user = User::find($request->user_id);
        // If user not found, return appropriate response
        if (!$user) {
            return ApiResponse::notFound(['error' => 'User not found']);
        }
        try {
            $mappedWarehouseDepots = getMappedUserDataByUserId($user->id);
            if ($mappedWarehouseDepots->isEmpty()) {
                return ApiResponse::forbidden('Unauthorized: No assigned warehouse or depot.');
            }

            $mappedwarehouseIds = $mappedWarehouseDepots->pluck('warehouse_id')->filter();
            $mappeddepotIds = $mappedWarehouseDepots->pluck('depo_id')->filter();

            switch ($user->role->name ?? null) {
                case env('warehouseHead'):
                    $query = Inventory::orderByDesc('id');

                    $data = $query->whereIn('source_id', $mappedwarehouseIds)->where('inventory_type_id', getInventoryTypeBySlug('warehouse'))
                        ->get()->groupBy('product_id')
                        ->map(function ($product) {
                            return (object) [
                                'product_id' => $product->first()->product_id,
                                'product_name' => $product->first()->product->name ?? "",
                                'locations' => $product->map(function ($item) {
                                    return $item->source()->name ?? "";
                                }),
                                'total_quantity' => $product->sum('quantity')
                            ];
                        })->values();

                    if ($data->isNotEmpty()) {

                        return ApiResponse::success(["products" => $data]);
                    } else {
                        return ApiResponse::success(["products" => []]);
                    }
                    break;
                case env('depotHead'):
                    $query = Inventory::orderByDesc('id');

                    $data = $query->whereIn('source_id', $mappeddepotIds)->where('inventory_type_id', getInventoryTypeBySlug('depot'))
                        ->get()->groupBy('product_id')
                        ->map(function ($product) {
                            return (object) [
                                'product_id' => $product->first()->product_id,
                                'product_name' => $product->first()->product->name ?? "",
                                'locations' => $product->map(function ($item) {
                                    return $item->source()->name ?? "";
                                }),
                                'total_quantity' => $product->sum('quantity')
                            ];
                        })->values();

                    if ($data->isNotEmpty()) {

                        return ApiResponse::success(["products" => $data]);
                    } else {
                        return ApiResponse::success(["products" => []]);
                    }
                    break;
                default:
                    return ApiResponse::forbidden('Unauthorized: User does not have the required role for this action');
            }
        } catch (\Exception $e) {
            return ApiResponse::internalServerError($e->getMessage());
        }
    }
    public function getProductDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'product_id' => 'required|exists:product_masters,id',
            ]);

            if ($validator->fails()) {
                return ApiResponse::validationError($validator->errors()->first());
            }

            $user = User::find($request->user_id);

            $mappedWarehouseDepots = getMappedUserDataByUserId($user->id);

            if ($mappedWarehouseDepots->isEmpty()) {
                return ApiResponse::forbidden('Unauthorized: No assigned warehouse or depot.');
            }

            $mappedwarehouseIds = $mappedWarehouseDepots->pluck('warehouse_id')->filter();
            $mappeddepotIds = $mappedWarehouseDepots->pluck('depo_id')->filter();

            switch ($user->role->name ?? null) {
                case env('warehouseHead'):
                    $data = $this->getInventoryData($mappedwarehouseIds, $request->product_id, 'warehouse');
                    break;

                case env('depotHead'):
                    $data = $this->getInventoryData($mappeddepotIds, $request->product_id, 'depot');
                    break;

                default:
                    return ApiResponse::forbidden('Unauthorized: User does not have the required role for this action');
            }
            if ($data) {
                return ApiResponse::success($data);
            } else {
                return ApiResponse::notFound("No product data available for the specified conditions.");
            }
        } catch (\Exception $e) {
            return ApiResponse::internalServerError($e->getMessage());
        }
    }

    private function getInventoryData($sourceIds, $productId, $inventoryType)
    {
        $inventoryQuery = Inventory::whereIn('source_id', $sourceIds)
            ->where('inventory_type_id', getInventoryTypeBySlug($inventoryType))
            ->where('product_id', $productId);

        $data = $inventoryQuery->get()->groupBy('product_id')
            ->map(function ($product) use ($sourceIds, $inventoryType) {
                return (object) [
                    'product_id' => $product->first()->product_id ?? "",
                    'product_name' => $product->first()->product->name ?? "",
                    'category' => $product->first()->product->category->name ?? "",
                    'company_name' => $product->first()->product->company->name ?? "",
                    'total_quantity' => $product->sum('quantity'),
                    'availability' => $product->map(function ($item) {
                        return [
                            'location_name' => $item->source()->name ?? "",
                            'quantity' => $item->quantity
                        ];
                    }),
                    'history' => InventroyHistory::where('product_id', $product->first()->product_id)
                        ->whereIn('source_id', $sourceIds)
                        ->where('inventory_type_id', getInventoryTypeBySlug($inventoryType))
                        ->orderByDesc('created_at')
                        ->get()
                        ->map(function ($history) {
                            $message = "";

                            switch ($history->action) {
                                case 'Stocked':
                                    $message = "Added {$history->quantity} {$history->product->uom->name} of {$history->product->name} to {$history->source()->name}";
                                    break;
                                case 'Transferred':
                                    $message = "Transferred {$history->quantity} {$history->product->uom->name} of {$history->product->name} from {$history->source()->name} to {$history->outsource()->name}";
                                    break;
                                case 'Received':
                                    $message = "Received {$history->quantity} {$history->product->uom->name} of {$history->product->name} from {$history->outsource()->name} to {$history->source()->name}";
                                    break;
                                default:
                                    $message = "Unknown action: {$history->action}";
                                    break;
                            }

                            return [
                                'user_name' => $history->user->name ?? "",
                                'status' => $history->action ?? "",
                                'date' => $history->created_at->format('Y-m-d H:i:s'),
                                'message' => $message,
                            ];
                        }),
                ];
            })->values()->first();

        return $data;
    }

    public function getConsignments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->first());
        }

        // Retrieve the user
        $user = User::find($request->user_id);
        // If user not found, return appropriate response
        if (!$user) {
            return ApiResponse::notFound(['error' => 'User not found']);
        }
        try {
            $mappedWarehouseDepots = getMappedUserDataByUserId($user->id);
            if ($mappedWarehouseDepots->isEmpty()) {
                return ApiResponse::forbidden('Unauthorized: No assigned warehouse or depot.');
            }

            $mappedwarehouseIds = $mappedWarehouseDepots->pluck('warehouse_id')->filter();
            $mappeddepotIds = $mappedWarehouseDepots->pluck('depo_id')->filter();

            switch ($user->role->name ?? null) {
                case env('warehouseHead'):
                    $query = Consignment::orderByDesc('created_at');

                    $query->whereIn('destination_source_id', $mappedwarehouseIds)->where('destination_source_type_id', getInventoryTypeBySlug('warehouse'));


                    $data = $query->get()->map(function ($con) {
                        return [
                            'consignment_id' => $con->id,
                            'consignment_number' => env('PrefixCon') . $con->id,
                            'status' => $con->status,
                            'origin_location' => $con->origin_source()->name,
                            // 'destination_location'=>$con->destination_source()->name,
                            'delivery_by_date' => $con->delivery_by_date,
                        ];
                    });
                    if ($data->isNotEmpty()) {

                        return ApiResponse::success(["consignments" => $data]);
                    } else {
                        return ApiResponse::success(["consignments" => []]);
                    }
                    break;
                case env('depotHead'):
                    $query = Consignment::orderByDesc('created_at');

                    $query->whereIn('destination_source_id', $mappeddepotIds)->where('destination_source_type_id', getInventoryTypeBySlug('depot'));


                    $data = $query->get()->map(function ($con) {
                        return [
                            'consignment_id' => $con->id,
                            'consignment_number' => env('PrefixCon') . $con->id,
                            'status' => $con->status,
                            'origin_location' => $con->origin_source()->name,
                            // 'destination_location'=>$con->destination_source()->name,
                            'delivery_by_date' => dateformat($con->delivery_by_date, 'Y-m-d')
                        ];
                    });
                    if ($data->isNotEmpty()) {
                        return ApiResponse::success(["consignments" => $data]);
                    } else {
                        return ApiResponse::success(["consignments" => []]);
                    }
                    break;
                default:
                    return ApiResponse::forbidden('Unauthorized: User does not have the required role for this action');
            }
        } catch (\Exception $e) {
            return ApiResponse::internalServerError($e->getMessage());
        }
    }
    public function getConsignmentDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'consignment_id' => 'required|exists:consignements,id',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->first());
        }
        try {
            $consignment = Consignment::find($request->consignment_id);
            if ($consignment->status !== 'delivered') {
                return ApiResponse::forbidden('This consignment has not been delivered yet.');
            }

            if ($consignment) {
                $data['consignment_id'] = $consignment->id;
                $data['trip_no'] = env('PrefixTrip').$consignment->trip_assigned_cons->trip->id ?? "";
                $data['consignment_number'] = env('PrefixCon') . $consignment->id;
                $data['vehicle_number'] = $consignment->trip_assigned_cons->trip->vehicle->vehicle_number ?? "";
                $data['driver_name'] = $consignment->trip_assigned_cons->trip->user->name ?? "";
                $data['status'] = $consignment->status ?? "";
                $data['is_checkout'] = false;
                $data['checkout_products'] = [];

                $data['is_checkout'] = $consignment->checkout_data ? true : false;
                $data['checkout_products'] = $consignment->checkout_data ? $consignment->checkout_data->checkout_products->map(function ($product) {
                    return [
                        'product_id' => $product->product_id,
                        'product_name' => $product->product->name,
                        'actual_quantity' =>  $product->actual_quantity,
                        'missing_damage_quantity' => $product->missing_damage_quantity,
                        'is_extra_missing' => $product->is_extra_missing,
                        'description' => $product->description,
                    ];
                }) : [];

                $data['products'] = $consignment->products->map(function ($product) {
                    return [
                        'product_id' => $product->product_id,
                        'product_name' => $product->product->name,
                        'quantity' => strval($product->quantity),
                    ];
                });
                $data['documents'] = [];

                if ($consignment->delivery_challan) {
                    $data['documents'][] = [
                        'document_name' => 'Delivery Challan',
                        'document_url' => asset('storage/' . $consignment->delivery_challan->document_path),
                    ];
                }

                if ($consignment->eway_bill) {
                    $data['documents'][] = [
                        'document_name' => 'E-Way Bill',
                        'document_url' => asset('storage/' . $consignment->eway_bill->document_path),
                    ];
                }
                return ApiResponse::success($data);
            }
        } catch (\Exception $e) {
            return ApiResponse::internalServerError($e->getMessage());
        }
    }

    // checkout consignment
    public function checkoutConsignments(Request $request)
    {
        // return $request;
        try {
            // Start a database transaction
            DB::beginTransaction();
            // Validate the request
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'consignment_id' => [
                    'required',
                    'integer',
                    'exists:consignements,id',
                    'unique:checkout_consignements,consignment_id',
                ],
                'products' => 'required|array',
                'products.*.product_id' => 'required|integer',
                'products.*.actual_quantity' => 'required',
                'products.*.missing_damage_quantity' => 'required',

                'products.*.is_extra_missing' => 'required|boolean',
                'products.*.description' => 'required_if:products.*.is_extra_missing,true',
            ], [
                'consignment_id.required' => 'The consignment ID is required.',
                'consignment_id.integer' => 'The consignment ID must be an integer.',
                'consignment_id.exists' => 'The selected consignment ID does not exist.',
                'consignment_id.unique' => 'The consignment has already been taken in Checkout Consignments.',

                'products.required' => 'The products field is required.',
                'products.array' => 'The products must be an array.',

                'products.*.product_id.required' => 'The product ID is required for all products.',
                'products.*.product_id.integer' => 'The product ID must be an integer.',

                'products.*.actual_quantity.required' => 'The actual quantity is required for all products.',
                'products.*.actual_quantity.integer' => 'The actual quantity must be an integer.',

                'products.*.missing_damage_quantity.required' => 'The missing/damage quantity is required for all products.',
                'products.*.missing_damage_quantity.integer' => 'The missing/damage quantity must be an integer.',

                'products.*.is_extra_missing.required' => 'The is_extra_missing field is required.',
                'products.*.is_extra_missing.boolean' => 'The is_extra_missing field must be a boolean.',
                'products.*.description.required_if' => 'The description field is required when is_extra_missing is true.',
            ]);
            // If validation fails, return error response
            if ($validator->fails()) {
                $errors['error'] = $validator->errors()->first();
                return ApiResponse::validationError($errors);
            }

            $consignment = Consignment::find($request->consignment_id);
            $now = Carbon::now();
            $user=User::find($request->user_id);
            if ($consignment) { 

                $checkoutConsignment = CheckoutConsignement::updateOrCreate(
                    ['consignment_id' => $request->input('consignment_id')],
                    [
                        'user_id' => $request->user_id,
                        'origin_source_type_id' => $consignment->origin_source_type_id,
                        'origin_source_id' => $consignment->origin_source_id,
                        'destination_source_type_id' => $consignment->destination_source_type_id,
                        'destination_source_id' => $consignment->destination_source_id,
                        'date' => $now,
                    ]
                );

                // Save missingDamageQty and actualQty using checkoutProducts relationship
                $this->saveProducts($checkoutConsignment, $request->input('products'));
                $this->addInventory($checkoutConsignment,$user);

                $data = [
                    'consignment_id' => $checkoutConsignment->consignment_id,
                    'is_checkout' => $consignment->checkout_data !== null,
                    'checkout_products' => $consignment->checkout_data
                        ? $consignment->checkout_data->checkout_products->map(function ($product) {
                            return [
                                'product_id' => $product->product_id,
                                'product_name' => $product->product->name,
                                'actual_quantity' => $product->actual_quantity,
                                'missing_damage_quantity' => $product->missing_damage_quantity,
                                'is_extra_missing' => $product->is_extra_missing,
                                'description' => $product->description,
                            ];
                        })
                        : [],
                ];
                
                DB::commit();
                
                return ApiResponse::created($data, 'Consignment checked out successfully.');                
            }
            return ApiResponse::notFound('This trip id does not exist');
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // Log::error($e->getMessage());
            dd( $e);
            return ApiResponse::internalServerError($e->getMessage());
        }
    }

    private function saveProducts($consignment, $products)
    {
        foreach ($products as $key => $product) {
            if ($product) {
                $is_extra_missing = $product['is_extra_missing'];

                $consignment->checkout_products()->updateOrCreate(
                    ['product_id' => $product['product_id']],
                    [
                        'actual_quantity' => $product['actual_quantity'],
                        'missing_damage_quantity' => $product['missing_damage_quantity'],
                        'is_extra_missing' =>  $is_extra_missing,
                        'description' => $is_extra_missing ? $product['description'] : ""
                    ]
                );
            }
        }
    }

    private function addInventory($checkoutConsignment,$user)
    {
        if ($checkoutConsignment->consignment) {
            foreach ($checkoutConsignment->checkout_products as $checkoutProduct) {
                $product = Inventory::where([
                    'product_id' => $checkoutProduct->product_id,
                    'inventory_type_id' => $checkoutConsignment->consignment->origin_source_type_id,
                    'source_id' =>  $checkoutConsignment->consignment->origin_source_id,
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
                        $checkoutConsignment->consignment->origin_source_type_id,
                        $checkoutConsignment->consignment->origin_source_id,
                        $checkoutConsignment->consignment->destination_source_type_id,
                        $checkoutConsignment->consignment->destination_source_id,
                        $transferQuantity,
                        'Transferred',
                        $admin,
                    );

                    // Update or create destination product
                    $this->addProductInDestination(
                        $checkoutConsignment,
                        $checkoutProduct->product_id,
                        $checkoutConsignment->consignment->destination_source_id,
                        $checkoutConsignment->consignment->destination_source_type_id,
                        $transferQuantity,
                        $user
                    );

                    // check product quantity is reached min level
                    checkMinStockLevelOrSendNotification($product->product_id,$product->source_id,$product->inventory_type_id);
                }
            }
        }
    }

    private function addProductInDestination($checkoutConsignment,$product_id, $destination_id, $destination_type_id, $quantity, $user)
    {
        // dd($product_id);
        $available_quantity = 0;
        $product = Inventory::where([
            'product_id' => $product_id,
            'inventory_type_id' => $destination_id,
            'source_id' =>  $destination_type_id,
        ])->first();

        if ($product) {
            $quantity += intval($product->quantity);
            $available_quantity += intval($product->available_quantity);

            $entry = $product->update(['quantity' => $quantity]);
        } else {
            // Product doesn't exist in the destination, create a new entry
            $entry=Inventory::create([
                'product_id' => $product_id,
                'inventory_type_id' => $destination_type_id,
                'source_id' => $destination_id,
                'quantity' => $quantity,
                'available_quantity' => $quantity,
            ]);
            $product=$entry;
        }
   
        if ($entry) {
            $this->createInventoryHistory(
                $product,
                $checkoutConsignment->consignment->destination_source_type_id,
                $checkoutConsignment->consignment->destination_source_id,
                $checkoutConsignment->consignment->origin_source_type_id,
                $checkoutConsignment->consignment->origin_source_id,
                $quantity,
                'Received',
                $user,
            );
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
        $user = $user; // Use provided user or fallback to authenticated user

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
}
