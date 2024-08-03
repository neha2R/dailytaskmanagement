<?php

namespace App\Http\Controllers\api\project_management;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\ProjectSubTasks;
use App\Models\SiteInventory;
use App\Models\WorkProgress;
use App\Models\WorkProgressProducts;
use App\Models\WorkProgressVehicles;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class ProgressReportController extends Controller
{
    public function storeProgressReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_task_id' => 'required|exists:project_sub_tasks,id',
            'user_id' => 'required|exists:users,id',
            'progress_quantity' => 'required|numeric|min:0',
            'uom_id' => 'required|exists:uoms,id',

            'products' => 'array',
            'products.*.product_id' => 'required',
            'products.*.quantity' => 'required|numeric|min:0',

            'labour_used' => 'required|boolean',
            'labour_quantity' => 'required_if:labour_used,true|numeric|min:0',

            'mason_used' => 'required|boolean',
            'mason_quantity' => 'required_if:mason_used,true|numeric|min:0',

            'machinery_used' => 'required|boolean',
            'machinery' => 'required_if:machinery_used,true|array',
            'machinery.*.vehicle_id' => 'required|exists:vehicles,id',
            'machinery.*.total_duration_minutes' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $project_task = ProjectSubTasks::find($request->project_task_id);
            // return $project_task->projectJob;
            if (!$project_task) {
                return ApiResponse::notFound(['error' => 'This project task ID is not valid']);
            }

            $now = Carbon::now();

            $workProgressData = [
                'project_sub_task_id' => $project_task->id,
                'project_id' => $project_task->projectJob->project_id,
                'site_id' => $project_task->projectJob->site_id,
                'user_id' => $request->user_id,
                'sub_task_id' => $project_task->sub_task_id,
                'work_date' => $now,
                'progress_quantity' => $request->progress_quantity,
                'uom_id' => $request->uom_id,
                'labour_used' => $request->labour_used,
                'labour_quantity' => $request->labour_quantity,
                'mason_used' => $request->mason_used,
                'mason_quantity' => $request->mason_quantity,
                'machinery_used' => $request->machinery_used,
            ];

            $workProgress = WorkProgress::create($workProgressData);

            if ($request->products) {
                $uniqueProductIds = collect($request->products)->pluck('product_id')->unique();
                if ($uniqueProductIds->count() !== count($request->products)) {
                    // Duplicate product_id found
                    return response()->json(['errors' => ['products' => ['Product IDs must be unique.']]], 422);
                }

                foreach ($request->products as $productData) {
                    $productId = $productData['product_id'];
                    $requestedQuantity = $productData['quantity'];

                    // Retrieve the existing quantity for the product
                    $existingQuantity = SiteInventory::where([
                        'product_id' => $productId,
                        'site_id' => $project_task->projectJob->site_id,
                        'project_id' => $project_task->projectJob->project_id
                    ])->lockForUpdate()->first();

                    if (!$existingQuantity) {
                        return response()->json(['errors' => ['products' => ['Product ID ' . $productId . ' not found in the inventory.']]], 422);
                    }

                    $existingQuantityValue = $existingQuantity->available_stock;

                    if ($requestedQuantity > $existingQuantityValue) {
                        // The requested quantity is greater than the existing quantity
                        return response()->json([
                            'errors' => ['products' => ['Requested quantity exceeds available quantity for product ID ' . $productId]]
                        ], 422);
                    }

                    // return response()->json([
                    //     'errors' => [
                    //         'products' => ['Requested quantity exceeds available quantity for product ID ' . $productId . '.']
                    //     ]
                    // ], 422);

                    // Subtract the requested quantity from the existing quantity
                    $updatedQuantity = $existingQuantityValue - $requestedQuantity;

                    // Update the site_inventory table with the new quantity
                    SiteInventory::where([
                        'product_id' => $productId,
                        'site_id' => $project_task->projectJob->site_id,
                        'project_id' => $project_task->projectJob->project_id
                    ])->update([
                        'available_stock' => $updatedQuantity,
                        'available_quantity' => $updatedQuantity,  // Added update for 'available_quantity'
                    ]);
                }

                $this->createWorkProgressProducts($workProgress->id, $request->products);
            }

            // Check uniqueness for machinery
            if ($request->machinery) {
                $uniqueVehicleIds = collect($request->machinery)->pluck('vehicle_id')->unique();

                if ($uniqueVehicleIds->count() !== count($request->machinery)) {
                    // Duplicate vehicle_id found
                    return response()->json(['errors' => ['machinery' => ['Vehicle IDs must be unique.']]], 422);
                }

                $this->createWorkProgressMachinery($workProgress->id, $request->machinery);
            }

            DB::commit();

            return ApiResponse::created([], 'progress stored successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function createWorkProgressProducts($workProgressId, $products)
    {
        foreach ($products as $product) {
            WorkProgressProducts::create([
                'work_progress_id' => $workProgressId,
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
            ]);
        }
    }

    private function createWorkProgressMachinery($workProgressId, $machinery)
    {
        foreach ($machinery as $machineryItem) {
            WorkProgressVehicles::create([
                'work_progress_id' => $workProgressId,
                'vehicle_id' => $machineryItem['vehicle_id'],
                'total_duration_minutes' => $machineryItem['total_duration_minutes'],
            ]);
        }
    }

    public function getSiteProducts(Request $request)
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
            $data = SiteInventory::where(['site_id' => $request->site_id, 'project_id' => $request->project_id])->get();
            if ($data->isNotEmpty()) {
                $data = $data->map(function ($item) {
                    return [
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name ?? "",
                        'available_stock' => $item->available_stock,
                    ];
                });
                return ApiResponse::success(['products' => $data]);
            } else {
                return ApiResponse::success(['products' => []]);
            }
        } catch (\Exception $e) {
            return ApiResponse::internalServerError($e->getMessage());
        }
    }
    public function getProgress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'project_task_id' => 'required|exists:project_sub_tasks,id',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->first());
        }
        $subTask = ProjectSubTasks::find($request->project_task_id);

        try {
            if ($subTask) {

                $response['project_task_id'] = $subTask->project_sub_task_id;
                $response['task_name'] = $subTask->subtask->name;

                $data = $subTask->progress;
                // return $data;
                if ($data->isNotEmpty()) {
                    $data = $data->map(function ($item) {
                        return [
                            'date' => dateformat($item->work_date, 'Y-m-d H:i:s'),
                            'progress_quantity' => $item->progress_quantity,
                            'uom' => $item->uom->name ?? "",
                            'products' => $item->products->map(function ($product) {
                                return [
                                    'product_id' => $product->id,
                                    'product_name' => $product->product->name,
                                    'quantity' => $product->quantity,
                                ];
                            }),

                            'labour_used' => $item->mason_used,
                            'labour_quantity' => $item->labour_quantity ?? "",

                            'mason_used' => $item->mason_used,
                            'mason_quantity' => $item->mason_quantity ?? "",

                            'machinery_used' => $item->machinery_used,
                            'machinery' => $item->machinery->map(function ($vehicle) {
                                return [
                                    'vehicle_number' => $vehicle->vehicle_id,
                                    'vehicle_model' => $vehicle->vehicle->model->name ?? "",
                                    'total_duration_minutes' => $vehicle->total_duration_minutes,
                                ];
                            }),

                        ];
                    });
                    return ApiResponse::success(['progress' => $data]);
                } else {
                    return ApiResponse::success(['progress' => []]);
                }
            }
        } catch (\Exception $e) {
            return ApiResponse::internalServerError($e->getMessage());
        }
    }
}
