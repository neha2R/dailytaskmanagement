<?php

namespace App\Http\Controllers\api\project_management;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use App\Models\ProductMaster;
use App\Models\Project;
use App\Models\ProjectJobs;
use App\Models\ProjectSubTasks;
use App\Models\RequestProducts;
use App\Models\SiteInventory;
use App\Models\User;
use App\Models\Vehicles;
use App\Models\Vendor;
use App\Notifications\SendPushNotification;
use App\Notifications\WebNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProjectManagementController extends Controller
{
    public function getProjects(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
            ]);
            // If validation fails, return error response
            if ($validator->fails()) {
                $errors['error'] = $validator->errors()->first();
                return ApiResponse::validationError($errors);
            }
            // Retrieve the user
            $user = User::find($request->user_id);

            // If user not found, return appropriate response
            if (!$user) {
                return ApiResponse::notFound(['error' => 'User not found']);
            }

            // Perform role-based logic
            switch ($user->role->name ?? null) {
                case env('SiteHeadRole'):
                    $projectsIds = ProjectJobs::where('site_head_id', $user->id)->pluck('project_id')->unique()->values();
                    $projects = Project::whereIn('id', $projectsIds)->get();
                    if ($projects->isNotEmpty()) {
                        $data = $projects->map(function ($project) {
                            return [
                                'id' => $project->id,
                                'project_name' => $project->project_name,
                                'contract_number' => $project->contract_number,
                                'start_date' => $project->start_date,
                                'end_date' => $project->start_date,
                                'status' => $project->status,
                                'progress' => $project->calculateProgress(),
                            ];
                        });
                        return ApiResponse::success(["projects" => $data]);
                    } else {
                        return ApiResponse::success(["projects" => []]);
                    }
                    break;
                case env('DivisionHeadRole'):
                    $projectsIds = ProjectJobs::where('division_head_id', $user->id)->pluck('project_id')->unique()->values();
                    $projects = Project::whereIn('id', $projectsIds)->get();
                    if ($projects->isNotEmpty()) {
                        $data = $projects->map(function ($project) {
                            return [
                                'id' => $project->id,
                                'project_name' => $project->project_name,
                                'contract_number' => $project->contract_number,
                                'start_date' => $project->start_date,
                                'end_date' => $project->start_date,
                                'status' => $project->status,
                                'progress' => $project->calculateProgress(),
                            ];
                        });
                        return ApiResponse::success(["projects" => $data]);
                    } else {
                        return ApiResponse::success(["projects" => []]);
                    }
                default:
                    return ApiResponse::forbidden('Unauthorized: User does not have the required role for this action');
            }
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // Log::error($e->getMessage());
            // return $e;
            return ApiResponse::internalServerError($e);
        }
    }

    public function getJobsByProjectId(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id',
                'project_id' => 'required|exists:projects,id',
            ]);
            // If validation fails, return error response
            if ($validator->fails()) {
                $errors['error'] = $validator->errors()->first();
                return ApiResponse::validationError($errors);
            }
            // Retrieve the user
            $user = User::find($request->user_id);
            // If user not found, return appropriate response
            if (!$user) {
                return ApiResponse::notFound(['error' => 'User not found']);
            }
            $project = Project::find($request->project_id);
            if (!$project) {
                return ApiResponse::notFound(['error' => 'Project not found']);
            }
            // Check user role and perform role-based logic
            switch ($user->role->name ?? null) {
                case env('SiteHeadRole'):
                    $data = $project->jobs()->where('site_head_id',$user->id)->get()->map(function ($value) {
                        return [
                            'id' => $value->id,
                            'job_id' => env('PrefixJob') . $value->id,
                            'project_id' => $value->project_id,
                            'site_id' => $value->site_id,
                            'job_name' => $value->job->name,
                            'start_date' => $value->start_date,
                            'end_date' => $value->end_date,
                            'status' => $value->status,
                            'progress' => $value->calculateProgress(),

                        ];
                    });
                    break;
                case env('DivisionHeadRole'):
                    $data = $project->jobs->map(function ($value) {
                        return [
                            'id' => $value->id,
                            'project_id' => $value->project_id,
                            'site_id' => $value->site_id,
                            'job_id' => env('PrefixJob') . $value->id,
                            'job_name' => $value->job->name,
                            'start_date' => $value->start_date,
                            'end_date' => $value->end_date,
                            'status' => $value->status,
                            'site_head' => $value->site_head->name,
                            'progress' => $value->calculateProgress(),
                        ];
                    });
                    break;
                default:
                    return ApiResponse::forbidden('User does not have the required role for this action');
            }

            // Return success response with transformed job data
            return ApiResponse::success(["jobs" => $data]);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // Log::error($e->getMessage());

            // Return internal server error response with a generic message
            return ApiResponse::internalServerError('An unexpected error occurred');
        }
    }

    public function  getJobDetails(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'jobId' => 'required',
            ]);
            // If validation fails, return error response
            if ($validator->fails()) {
                $errors['error'] = $validator->errors()->first();
                return ApiResponse::validationError($errors);
            }
            $job = ProjectJobs::find($request->jobId);
            if (!$job) {
                return ApiResponse::notFound(['error' => 'Job not found']);
            }
            $data['job_id'] = env('PrefixJob') . $job->id;
            $data['division'] = $job->division->name;
            $data['sub_division'] = $job->subdivision->name;
            $data['ward_name'] = $job->site->ward_name ?? "";
            $data['site'] = $job->site->name;
            $data['inputs'] = [];
            if ($job->inputs) {
                $data['inputs'] = $job->inputs->map(function ($value) {
                    return [
                        'input_name' => $value->input->name,
                        'value' => $value->value,
                        'uom' => $value->uom->name ?? "",
                    ];
                });
            }
            return ApiResponse::success($data);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // Log::error($e->getMessage());

            // Return internal server error response with a generic message
            return ApiResponse::internalServerError('An unexpected error occurred');
        }
    }

    public function  getSubTasks(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'jobId' => 'required',
            ]);
            // If validation fails, return error response
            if ($validator->fails()) {
                $errors['error'] = $validator->errors()->first();
                return ApiResponse::validationError($errors);
            }
            $job = ProjectJobs::find($request->jobId);
            if (!$job) {
                return ApiResponse::notFound(['error' => 'Job not found']);
            }
            $data['job_id'] = env('PrefixJob') . $job->id;
            $data['job_name'] = $job->job->name;
            $data['total_tasks'] = $job->subtasks->count();
            $data['ongoing_tasks'] = $job->subtasks->where('status', 'in-process')->count();
            $data['completed_tasks'] = $job->subtasks->where('status', 'completed')->count();
            $data['tasks'] = [];
            if ($job->subtasks) {
                $data['tasks'] = $job->subtasks->map(function ($value) {
                    return [
                        'project_task_id' => $value->id,
                        'task_name' => $value->subtask->name,
                        'status' => $value->status,
                        'start_date' => $value->start_date ?? "",
                        'end_date' => $value->start_date ?? "",
                        'sub_contract'=>$value->sub_contract,
                        'vendor_name'=>$value->vendor->name ?? "",
                        'value' => $value->value ?? "",
                        'uom' => $value->uom ?? "",
                    ];
                });
            }
            return ApiResponse::success($data);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            // Log::error($e->getMessage());

            // Return internal server error response with a generic message
            return ApiResponse::internalServerError('An unexpected error occurred');
        }
    }

    public function startSubTask(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'project_task_id' => 'required|exists:project_sub_tasks,id',
                'sub_contract' => 'required|boolean',
                'vendor_id' => 'required_if:sub_contract,true|exists:vendors,id'
            ]);

            // If validation fails, return error response
            if ($validator->fails()) {
                return ApiResponse::validationError($validator->errors()->first());
            }

            $subTask = ProjectSubTasks::find($request->project_task_id);

            if (!$subTask) {
                return ApiResponse::notFound(['error' => 'Task is not found']);
            }

            if ($subTask->status !== 'to-do') {
                $message = 'Cannot perform this action. Task status: ' . $subTask->status;
                return ApiResponse::forbidden($message);
            }

            // Update task details
            $now = Carbon::now();
            $subTask->update(['start_date' => $now, 'status' => 'in-process', 'sub_contract' => $request->sub_contract, 'vendor_id' => $request->sub_contract ? $request->vendor_id : null]);

            $job = ProjectJobs::find($subTask->projects_jobs_id);
            if ($job && $job->status == 'to-do') {
                $job->update(['status' => 'in-process', 'startrd_at' => now()]);
                if ($job->project->status == 'to-do') {
                    $job->project->update(['status' => 'in-process', 'started_at' => now()]);
                }
            }
            // Prepare response data
            $data = [
                'project_task_id' => $subTask->id,
                'task_name' => $subTask->subtask->name,
                'start_date' => $subTask->start_date->format('Y-m-d'),
                'end_date' => $subTask->end_date ? $subTask->end_date->format('Y-m-d') : "",
                'status' => $subTask->status,
            ];

            return ApiResponse::success($data);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error($e->getMessage());

            // Return internal server error response with a generic message
            return ApiResponse::internalServerError($e->getMessage());
        }
    }

    public function getInventoriesItems(Request $request)
    {
        //    Validate the request
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|exists:sites,id',
            'project_id' => 'required|exists:projects,id',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->first());
        }


        try {
            $products = SiteInventory::where(['site_id' => $request->site_id, 'project_id' => $request->project_id])->get();

            if ($products->isNotEmpty()) {
                $products = $products->map(function ($product) {
                    return [
                        'product_id' => $product->product_id,
                        'product_name' => $product->product->name,
                        'current_stock' => $product->available_stock,
                        'received' => $product->received_stock,
                    ];
                });
                return ApiResponse::success(['products' => $products]);
            } else {
                return ApiResponse::success(['products' => []]);
            }
        } catch (\Exception $e) {
            return ApiResponse::internalServerError($e->getMessage());
        }
    }

    public function getAllRequests(Request $request)
    {
        //    Validate the request
        $validator = Validator::make($request->all(), [
            'site_id' => 'required|exists:sites,id',
            'project_id' => 'required|exists:projects,id',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->first());
        }


        try {
            $data = MaterialRequest::where(['site_id' => $request->site_id, 'project_id' => $request->project_id])->get();
            if ($data->isNotEmpty()) {
                $data = $data->map(function ($item) {
                    return [
                        'request_id' => $item->id,
                        'user_name' => $item->user->name ?? "",
                        'status' => $item->status,
                        'products_count' => $item->products_count,
                        'date' => dateformat($item->date, 'Y-m-d'),
                        'products' => $item->products->map(function ($product) {
                            return [
                                'product_id' => $product->product_id,
                                'product_name' => $product->product->name ?? "",
                                'quantity' => $product->quantity
                            ];
                        })
                    ];
                });
                return ApiResponse::success(['requests' => $data]);
            } else {
                return ApiResponse::success(['requests' => []]);
            }
        } catch (\Exception $e) {
            return ApiResponse::internalServerError($e->getMessage());
        }
    }

    public function createRequest(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'project_id' => 'required|exists:projects,id',
            'site_id' => 'required|exists:sites,id',
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:product_masters,id',
            'products.*.quantity' => 'required|numeric|min:0',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Create a material request
            $materialRequest = MaterialRequest::create([
                'project_id' => $request->project_id,
                'site_id' => $request->site_id,
                'user_id' => $request->user_id,
                'status' => 'pending',
                'date' => $request->date,
            ]);

            // Merge quantities for products with the same product_id
            $mergedProducts = collect($request->products)->groupBy('product_id')->map(function ($group) {
                return [
                    'product_id' => $group[0]['product_id'],
                    'quantity' => $group->sum('quantity'),
                ];
            })->values()->all();

            // Add merged products to the material request
            foreach ($mergedProducts as $product) {
                $materialRequest->products()->create([
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                ]);
                $materialRequest->increment('products_count');
            }

            $title = 'New Material Request Received';
            $siteName = $materialRequest->site->name;
            $message = "Material request received from site: $siteName. Please review.";
            $admin = User::whereNull('role_id')->first(); // Assuming the admin is the authenticated user
            $data = [
                'title' => $title,
                'message' => $message,
            ];

            // Create and send the notification
            $notification = new SendPushNotification($title, $message, $admin, $data);
            $admin->notify($notification);


            // web notification
            $admin->notify(new WebNotification(route('project-management.material-requests.index'), $title, $message));

            // Commit the transaction
            DB::commit();

            $response = [
                'request_id' => $materialRequest->id,
                'user_id' => $materialRequest->user_id,
                'site_id' => $materialRequest->site_id,
                'date' => dateformat($materialRequest->date, 'Y-m-d'),
                'status' => $materialRequest->status,
                'total_products' => $materialRequest->products_count,
            ];

            return ApiResponse::created($response, 'Material request created successfully');
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();
            // Return internal server error response with a generic message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function updateRequest(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'request_id' => 'required|exists:material_requests,id',
            'user_id' => 'required',
            'date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:product_masters,id',
            'products.*.quantity' => 'required|numeric|min:1',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Start a database transaction
            DB::beginTransaction();

            // Find the material request by ID
            $materialRequest = MaterialRequest::findOrFail($request->request_id);

            // Update material request data
            $materialRequest->update([
                'user_id' => $request->user_id,
                'date' => $request->date,
                'products_count' => 0
            ]);

            // Remove existing products associated with the material request
            $materialRequest->products()->delete();

            // Merge quantities for products with the same product_id
            $mergedProducts = collect($request->products)->groupBy('product_id')->map(function ($group) {
                return [
                    'product_id' => $group[0]['product_id'],
                    'quantity' => $group->sum('quantity'),
                ];
            })->values()->all();

            // Add merged products to the material request
            foreach ($mergedProducts as $product) {
                $materialRequest->products()->create([
                    'product_id' => $product['product_id'],
                    'quantity' => $product['quantity'],
                ]);
                $materialRequest->increment('products_count');
            }

            // Commit the transaction
            DB::commit();

            $response = [
                'request_id' => $materialRequest->id,
                'user_id' => $materialRequest->user_id,
                'site_id' => $materialRequest->site_id,
                'date' => dateformat($materialRequest->date, 'Y-m-d'),
                'status' => $materialRequest->status,
                'total_products' => $materialRequest->products_count,
            ];

            return ApiResponse::success($response, 'Material request updated successfully');
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollBack();
            // Return internal server error response with a generic message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getProducts()
    {
        $q = request('q');

        $query = ProductMaster::where('is_active', true);

        if ($q) {
            $query->where('name', 'like', '%' . $q . '%');
        }


        $products = $query->with('uom:id,name')->select('id', 'name', 'uom_id')->get();

        $products = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'uom' => $product->uom->name ?? "",
            ];
        });

        if ($products->isNotEmpty()) {
            return ApiResponse::success(['products' => $products]);
        }

        return ApiResponse::success(['products' => $products]);
    }
    public function getVendors()
    {
        $q = request('q');

        $query = Vendor::where('is_active', true);

        if ($q) {
            $query->where('name', 'like', '%' . $q . '%');
        }

        $vendors = $query->select('id', 'name',)->get();

        if ($vendors->isNotEmpty()) {
            return ApiResponse::success(['vendors' => $vendors]);
        }

        return ApiResponse::success(['vendors' => []]);
    }
    public function getVehicles()
    {
        $q = request('q');

        $query = Vehicles::where('is_active', true);

        if ($q) {
            $query->where('vehicle_number', 'like', '%' . $q . '%');
        }

        $vehicles = $query->get()->map(function ($vehicle) {
            return [
                'vehicle_id' => $vehicle->id,
                'vehicle_number' => $vehicle->vehicle_number,
                'vehicle_model' => $vehicle->model->name ?? "",
            ];
        });

        if ($vehicles->isNotEmpty()) {
            return ApiResponse::success(['vehicles' => $vehicles]);
        }

        return ApiResponse::success(['vehicles' => []]);
    }

    public function endSubTask(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'project_task_id' => 'required|exists:project_sub_tasks,id',
            ]);

            // If validation fails, return error response
            if ($validator->fails()) {
                return ApiResponse::validationError($validator->errors()->first());
            }

            $subTask = ProjectSubTasks::find($request->project_task_id);

            if (!$subTask) {
                return ApiResponse::notFound(['error' => 'Task is not found']);
            }

            if ($subTask->status !== 'in-process') {
                $message = 'Cannot perform this action. Task status: ' . $subTask->status;
                return ApiResponse::forbidden($message);
            }

            // Update task details
            $now = Carbon::now();
            $subTask->update(['end_date' => $now, 'status' => 'completed']);

            $job = ProjectJobs::find($subTask->projects_jobs_id);
            if ($job && $job->status == 'in-process') {
                // Check if all sub-tasks for this job are completed
                $allSubTasksCompleted = $job->subtasks()->where('status', '!=', 'completed')->doesntExist();

                if ($allSubTasksCompleted) {
                    // Update the ProjectJobs model
                    $job->update(['status' => 'completed', 'ended_at' => $now]);
                }
                // Check if all jobs for the project are completed
                $project = $job->project;
                $allJobsCompleted = $project->jobs()->where('status', '!=', 'completed')->doesntExist();

                if ($allJobsCompleted) {
                    // Update the Project model
                    $project->update(['status' => 'completed','ended_at'=>$now]);
                }
            }
            // Prepare response data
            $data = [
                'project_task_id' => $subTask->id,
                'task_name' => $subTask->subtask->name,
                'start_date' => dateformat($subTask->start_date, 'Y-m-d'),
                'end_date' => $subTask->end_date ? dateformat($subTask->end_date, 'Y-m-d') : "",
                'status' => $subTask->status,
            ];

            return ApiResponse::success($data);
        } catch (\Exception $e) {
            // Log the exception for debugging purposes
            Log::error($e->getMessage());

            // Return internal server error response with a generic message
            return ApiResponse::internalServerError($e->getMessage());
        }
    }
}
