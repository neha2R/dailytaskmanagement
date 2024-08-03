<?php

namespace App\Http\Controllers\api\project_management;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\CheckoutConsignement;
use App\Models\Consignment;
use App\Models\Inventory;
use App\Models\InventroyHistory;
use App\Models\ProductMaster;
use App\Models\SiteInventory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InventoryAndConsignmentController extends Controller
{
    public function getConsignments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|exists:sites,id',
            'project_id' => 'required|exists:projects,id',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->first());
        }

        try {
            $data = Consignment::where([
                'destination_source_id' => $request->site_id,
                'destination_source_type_id' => getInventoryTypeBySlug('site'),
                'status' => 'delivered',
                // 'project_id' => $request->project_id
            ])->orderByDesc('created_at')->get();
            if ($data->isNotEmpty()) {
                $consignments = $data->map(function ($con) {
                    return [
                        'project_id' => $con->project_id,
                        'site_id' => $con->destination_source_id,
                        'consignment_id' => $con->id,
                        'consignementNo' =>  env('PrefixCon') . $con->id,
                        'products' => $con->products->map(function ($product) {
                            return [
                                'product_id' => $product->product_id,
                                'product_name' => $product->product->name ?? "",
                                'quantity' => strval($product->quantity),
                            ];
                        }),
                        'is_checkout' => $con->checkout_data ? true : false,
                        'checkout_products' => $con->checkout_data ? $con->checkout_data->checkout_products->map(function ($product) {
                            return [
                                'product_id' => $product->product_id,
                                'product_name' => $product->product->name,

                                'actual_quantity' =>  $product->actual_quantity,
                                'missing_damage_quantity' => $product->missing_damage_quantity,
                            ];
                        }) : [],
                    ];
                });

                return ApiResponse::success(['consignments' => $consignments]);
            } else {
                return ApiResponse::success(['consignments' => []]);
            }
        } catch (\Exception $e) {
            return ApiResponse::internalServerError($e->getMessage());
        }
    }

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

                'products.*.is_extra_missing' => 'required|boolean',
                'products.*.description' => 'required_if:products.*.is_extra_missing,true',
            ]);
            // If validation fails, return error response
            if ($validator->fails()) {
                $errors['error'] = $validator->errors()->first();
                return ApiResponse::validationError($errors);
            }

            $consignment = Consignment::find($request->consignment_id);
            $now = Carbon::now();
            if ($consignment) {

                $checkoutConsignement = CheckoutConsignement::updateOrCreate(
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
                $this->saveProducts($checkoutConsignement, $request->input('products'));
                $this->addInventory($checkoutConsignement);

                $data = [];
                DB::commit();

                return ApiResponse::created($data, 'Consignment checked out successfully.');
            }
            return ApiResponse::notFound('This trip id does not exist');
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // Log::error($e->getMessage());
            return $e;
            return ApiResponse::internalServerError($e->getMessage());
        }
    }

    private function saveProducts($consignment, $products)
    {
        foreach ($products as $key => $product) {
            if ($product) {
                $is_extra_missing=$product['is_extra_missing'];

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
                    checkMinStockLevelOrSendNotification($product->product_id,$product->source_id,$product->inventory_type_id);


                    // Update or create destination product
                    $this->addProductInSite(
                        $checkoutProduct->product_id,
                        $checkoutConsignement->consignment->destination_source_id,
                        $checkoutConsignement->consignment->project_id ?? null,
                        $transferQuantity
                    );
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

    private function addProductInSite($productId, $siteId, $project_id, $quantity)
    {
        // Check if the product already exists in the site inventory
        $existingProduct = SiteInventory::where([
            'product_id' => $productId,
            'site_id' => $siteId,
        ])->first();
        $quantity = (float) $quantity;

        if ($existingProduct) {
            // Update quantities for existing product
            $existingProduct->update([
                'available_stock' => $existingProduct->available_stock + $quantity,
                'available_quantity' => $existingProduct->available_quantity + $quantity,
                'received_stock' => $existingProduct->received_stock + $quantity,
            ]);
        } else {
            // Create a new entry for the product in the site inventory
            SiteInventory::create([
                'project_id' => $project_id,
                'product_id' => $productId,
                'site_id' => $siteId,
                'available_stock' => $quantity,
                'available_quantity' => $quantity,
                'received_stock' => $quantity,
            ]);
        }
    }
}
