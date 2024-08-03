<?php

namespace App\Http\Controllers\project_management;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Inputs;
use App\Models\Jobs;
use App\Models\Project;
use App\Models\ProjectInputs;
use App\Models\ProjectJobs;
use App\Models\ProjectSubTasks;
use App\Models\Site;
use App\Models\SubDivision;
use App\Models\Vendor;
use App\Models\WorkProgress;
use App\Notifications\AndroidNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $assignedToUsers = getUsersByRoleName(['Chief executive officer']);

        // Get active vendors for dropdown
        $activeVendors = Vendor::where('is_active', true)->get(['name', 'id']);

        // Get projects ordered by creation date
        $projects = Project::latest()->get();

        return view('admin.project_management.projects.projects', compact('assignedToUsers', 'activeVendors', 'projects'));
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
        try {
            $validatedData = $request->validate([
                'project_name' => 'required|max:50',
                'contract_number' => 'required|max:50|unique:projects,contract_number',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'assigned_to' => 'required|exists:users,id',
                'vendor_id' => 'required|exists:vendors,id',
                'poc_name' => 'required|max:50',
                'contact_number' => 'required|numeric|digits:10',
                'email' => 'required|email',
            ]);
            // Format dates before insertion if needed
            $startDate = dateformat($validatedData['start_date'], 'Y-m-d');
            $endDate = dateformat($validatedData['end_date'], 'Y-m-d');

            // Create an array with the data to be inserted
            $insertData = [
                'project_name' => $validatedData['project_name'],
                'contract_number' => $validatedData['contract_number'],
                'start_date' => $startDate,
                'end_date' => $endDate,
                'assigned_to' => $validatedData['assigned_to'],
                'vendor_id' => $validatedData['vendor_id'],
                'poc_name' => $validatedData['poc_name'],
                'contact_number' => $validatedData['contact_number'],
                'email' => $validatedData['email']
            ];
            // Validation passed, create a new project
            $project = Project::create($insertData);
            if ($project) {
                // Send the TripCreateNotification
                $title = 'New Project Assigned';
                $message = 'You have been assigned to a new project.';
                $user = $project->user;
                $data = [
                    'notification_type' => "project_management",
                    'title' => $title,
                    'message' => $message,
                ];
                // Create and send the notification
                $notification = new AndroidNotification($user, $data);
                $user->notify($notification);
            }
            // Additional logic if needed
            return redirect()->back()->with('success', 'Project created successfully!');
        } catch (\Exception $e) {
            dd($e);
            // Log the exception or handle it in an appropriate way
            return redirect()->back()->with('error', 'Error creating project. Please try again.');
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
        $project = Project::findOrFail($id);
        $activeJobs = Jobs::where('is_active', true)->get(['id', 'name']);
        $activeDivisions = Division::where('is_active', true)->get(['id', 'name']);
        return view('admin.project_management.projects.projects_jobs', compact('project', 'activeJobs', 'activeDivisions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $project = Project::find($id);
        $project->start_date = dateformat($project->start_date, 'd M Y');
        $project->end_date = dateformat($project->end_date, 'd M Y');
        return response()->json(['status' => 200, 'project' => $project]);
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
        $id = $request->id ?? false;

        $validatedData = $request->validate([
            'id' => 'required',
            'project_name' => 'required|max:50',
            'contract_number' => 'required|max:50|unique:projects,contract_number,' . $id,
            'start_date' => 'required',
            'end_date' => 'required',
            'assigned_to' => 'required|exists:users,id',
            'vendor_id' => 'required|exists:vendors,id',
            'poc_name' => 'required|max:50',
            'contact_number' => 'required|numeric|digits:10',
            'email' => 'required|email',
        ]);
        // Format dates before updating if needed
        $startDate = dateformat($validatedData['start_date'], 'Y-m-d');
        $endDate = dateformat($validatedData['end_date'], 'Y-m-d');
        // Create an array with the data to be updated
        $updateData = [
            'project_name' => $validatedData['project_name'],
            'contract_number' => $validatedData['contract_number'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'assigned_to' => $validatedData['assigned_to'],
            'vendor_id' => $validatedData['vendor_id'],
            'poc_name' => $validatedData['poc_name'],
            'contact_number' => $validatedData['contact_number'],
            'email' => $validatedData['email']
        ];

        // Find the project by ID and update it
        $project = Project::findOrFail($id);
        $project->update($updateData);

        // Additional logic if needed
        return redirect()->back()->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $project = Project::find($id);
        if ($project) {
            $project->delete();
            return response()->json(['status' => 200, 'message' => 'Project has been deleted!']);
        } else {
            return response()->json(['status' => 200, 'message' => 'Something went wrong while deleting the project. Please try again.']);
        }
    }

    public function checkContractNumber(Request $request)
    {
        $name = $request->input('name');
        $isUnique = !Project::where('contract_number', $name)->exists();
        return response()->json(['unique' => $isUnique]);
    }

    public function addJobs($id)
    {
        $project = Project::findOrFail($id);
        $activeJobs = Jobs::where('is_active', true)->get(['id', 'name']);

        // $activeJobs  = Jobs::where('is_active', true)
        //     ->whereHas('subtasks', function ($query) {
        //         $query->where('sub_tasks_jobs.is_active', true);
        //     }, '>', 1)
        //     ->get();
        //     return $activeJobs;

        $activeDivisions = Division::where('is_active', true)->get(['id', 'name']);
        return view('admin.project_management.projects.add_job.add_jobs', compact('project', 'activeJobs', 'activeDivisions'));
    }

    public function getInputs($id)
    {
        $inputs = Jobs::find($id)->inputs()->wherePivot('is_active', true)->get();
        return response()->json(['inputs' => $inputs]);
    }
    public function getSubDivisions($id)
    {
        $user = Division::with('user')->find($id);
        $subDivisions = SubDivision::where(['is_active' => true, 'division_id' => $id])->get(['id', 'name']);
        return response()->json(['subDivisions' => $subDivisions, "user" => $user]);
    }
    public function getSites($id)
    {
        $sites = Site::where(['is_active' => true, 'sub_division_id' => $id])->get(['id', 'name']);
        return response()->json(['sites' => $sites]);
    }
    public function getSiteUser($id)
    {
        $user = Site::with('user')->find($id);
        return response()->json(['user' => $user]);
    }


    public function storeJob(Request $request)
    {
        $validatedData = $this->validateRequest($request);
        try {
            DB::beginTransaction(); // Start a database transaction

            // Prepare data for ProjectJob
            $dataToInsert = [
                'project_id' => $validatedData['project_id'],
                'job_id' => $validatedData['job_id'],
                'division_id' => $validatedData['division_id'],
                'sub_division_id' => $validatedData['sub_division_id'],
                'site_id' => $validatedData['site_id'],
                'division_head_id' => $validatedData['division_head_id'],
                'site_head_id' => $validatedData['site_head_id'],
                'start_date' => Carbon::parse($validatedData['start_date'])->format('Y-m-d'),
                'end_date' => Carbon::parse($validatedData['end_date'])->format('Y-m-d'),
            ];

            // Create ProjectJob using insert method
            $projectJob = ProjectJobs::create($dataToInsert);

            if (isset($validatedData['inputsValue']) && is_array($validatedData['inputsValue']) && count($validatedData['inputsValue']) > 0) {
                // Create ProjectInputs
                foreach ($validatedData['inputsValue'] as $inputId => $value) {
                    ProjectInputs::create([
                        'projects_jobs_id' => $projectJob->id,
                        'input_id' => $inputId,
                        'value' => $value,
                        'uom_id' => $validatedData['inputsUom'][$inputId],
                    ]);
                }
            }

            $subTasks = Jobs::find($validatedData['job_id'])->subtasks()->wherePivot('is_active', true)->get();

            if ($subTasks->isEmpty()) {
                DB::rollBack();
                return Redirect::back()->with('error', 'No active subtasks found for the selected job.');
            }

            foreach ($subTasks as $key => $value) {
                ProjectSubTasks::create([
                    'projects_jobs_id' => $projectJob->id,
                    'sub_task_id' => $value->id
                ]);
            }
            if ($projectJob) {
                // Send the AndroidNotification
                $title = 'New Job Assigned';
                $message = 'You have a new job assigned in the project';
                $user = $projectJob->site_head;

                $data = [
                    'notification_type' => 'project_management',
                    'title' => $title,
                    'message' => $message,
                ];

                // Create and send the notification
                $notification = new AndroidNotification($user, $data);
                $user->notify($notification);
            }
            DB::commit(); // Commit the database transaction

            return Redirect::route('project-management.projects.show', $request->project_id)->with('success', 'The job has been successfully added.');
        } catch (\Exception $e) {
            DB::rollBack();
            return $e;
            // Log the exception or handle it as needed
            return Redirect::back()->with('error', 'An error occurred while adding Job.');
        }
    }

    protected function validateRequest(Request $request)
    {
        return $request->validate([
            'project_id' => 'required|exists:projects,id',
            'job_id' => 'required|exists:jobs,id',
            'division_id' => 'required|exists:divisions,id',
            'sub_division_id' => 'required|exists:sub_divisions,id',
            'site_id' => 'required|exists:sites,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'division_head_id' => 'required|exists:users,id',
            'site_head_id' => 'required|exists:users,id',
            'inputsValue.*' => 'required|numeric',
            'inputsUom.*' => 'required',
        ]);
    }

    public function viewJob($id)
    {
        $job = ProjectJobs::findOrFail($id);
        // Fetch work progress for this project base job 
        $consumption = WorkProgress::whereIn('project_sub_task_id', $job->subtasks->pluck('id'))->get();
        // Extract consumption data
        $consumptionDetails = $consumption->flatMap(function ($progress) {
            // For each work progress record, map over its associated products
            return $progress->products->map(function ($product) use ($progress) {
                // For each product, create an array with its details
                return [
                    'product_name' => $product->product->name,
                    'quantity' => $product->quantity,
                    'date' => dateformat($progress->work_date, 'd M Y'),
                    'task_name' => $progress->sub_task->name ?? "",
                ];
            });
        });
        $consumptionDetails = $consumptionDetails
            ->sortByDesc('created_at')
            ->values()
            ->toArray();
        return view('admin.project_management.projects.jobs.view_job', compact('job', 'consumptionDetails'));
    }
    public function viewDailyReport($id)
    {
        $task = ProjectSubTasks::find($id);
        // return $task->progress[0]->machinery;
        return view('admin.project_management.projects.jobs.daily_reports', compact('task'));
    }
}
