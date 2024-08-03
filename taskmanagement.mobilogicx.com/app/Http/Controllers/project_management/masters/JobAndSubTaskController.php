<?php

namespace App\Http\Controllers\project_management\masters;

use App\Http\Controllers\Controller;
use App\Models\Inputs;
use App\Models\InputsJobs;
use App\Models\Jobs;
use App\Models\SubTasks;
use App\Models\SubTasksJobs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JobAndSubTaskController extends Controller
{
    public function jobs()
    {
        $jobs = Jobs::orderBy('name')->get();
        return view('admin.project_management.masters.jobs', compact('jobs'));
    }

    public function subtasks()
    {
        $subTasks = SubTasks::orderBy('name')->get();
        $active_jobs = Jobs::where('is_active', true)->get(['id', 'name']);
        return view('admin.project_management.masters.sub_tasks', compact('active_jobs', 'subTasks'));
    }

    public function inputs()
    {
        $inputs = Inputs::orderBy('name')->with('jobs')->get();
        // return $inputs;
        $active_jobs = Jobs::where('is_active', true)->get(['id', 'name']);
        return view('admin.project_management.masters.inputs', compact('active_jobs', 'inputs'));
    }

    public function checkUniqueJobName(Request $request)
    {
        $jobName = $request->input('name');
        // Check if the product name is unique
        $isUnique = !Jobs::where('name', $jobName)->exists();

        return response()->json(['unique' => $isUnique]);
    }

    public function checkUniqueSubTaskName(Request $request)
    {
        $jobName = $request->input('name');
        // Check if the product name is unique
        $isUnique = !SubTasks::where('name', $jobName)->exists();

        return response()->json(['unique' => $isUnique]);
    }

    public function checkUniqueInputName(Request $request)
    {
        $name = $request->input('name');
        // Check if the product name is unique
        $isUnique = !Inputs::where('name', $name)->exists();

        return response()->json(['unique' => $isUnique]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:jobs,name',
        ]);
        $dataToInsert = [
            'name' => $request->name,
        ];
        Jobs::create($dataToInsert);
        return redirect()->back()->with('success', 'The job has been successfully created.');
    }

    public function subtaskStore(Request $request)
    {
        $request->validate([
            // 'job_id' => 'required|exists:jobs_and_sub_tasks,id',
            'subtasks' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    $parentJobId = $request->job_id;
                    // Retrieve existing subtask names for the parent job
                    $existingSubtaskNames = DB::table('sub_tasks')
                        ->pluck('name')
                        ->toArray();

                    // Check if all subtask names are unique
                    if (count($value) !== count(array_unique($value)) || count(array_intersect($value, $existingSubtaskNames)) > 0) {
                        $fail('Subtasks must be unique.');
                    }
                },
            ],
        ]);

        foreach ($request->input('subtasks') as $key => $value) {
            $dataToInsert = [
                'name' => $value,
            ];
            SubTasks::create($dataToInsert);
        }
        return redirect()->back()->with('success', 'Subtasks have been successfully created.');
    }

    public function inputsStore(Request $request)
    {
        // return $request;
        $request->validate([
            // 'job_id' => 'required|exists:jobs_and_sub_tasks,id',
            'name' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    // Check if all elements in the array are unique
                    if (count($value) !== count(array_unique($value))) {
                        $fail('Inputs names must be unique.');
                    }
                },
            ],
        ]);

        foreach ($request->input('name') as $key => $value) {
            $dataToInsert = [
                'name' => $value,
            ];
            Inputs::create($dataToInsert);
        }

        return redirect()->back()->with('success', 'Inputs have been successfully created.');
    }

    public function status(Request $request)
    {
        Jobs::find($request->id)->update(['is_active' => $request->status === 'true' ? true : false]);
        return response()->json(['status' => 200, 'message' => 'Status changed successfully']);
    }
    public function subTaskstatus(Request $request)
    {
        SubTasks::find($request->id)->update(['is_active' => $request->status === 'true' ? true : false]);
        return response()->json(['status' => 200, 'message' => 'Status changed successfully']);
    }
    public function inputStatus(Request $request)
    {
        Inputs::find($request->id)->update(['is_active' => $request->status === 'true' ? true : false]);
        return response()->json(['status' => 200, 'message' => 'Status changed successfully']);
    }
    public function edit($id)
    {
        $data = Jobs::find($id);
        return response()->json(['status' => 200, 'data' => $data]);
    }
    public function editSubTask($id)
    {
        $data = SubTasks::find($id);
        return response()->json(['status' => 200, 'data' => $data]);
    }
    public function editInput($id)
    {
        $data = Inputs::find($id);
        return response()->json(['status' => 200, 'data' => $data]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required|unique:jobs,name,' . $request->id . ',id',
        ]);

        $dataToUpdate = [
            'name' => $request->name,
        ];

        Jobs::where('id', $request->id)->update($dataToUpdate);

        return redirect()->back()->with('success', 'The job has been successfully updated.');
    }

    public function subtaskUpdate(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:sub_tasks,id',
            'name' => 'required|unique:sub_tasks,name,' . $request->id . ',id',
        ]);

        // Find the specific subtask within the parent job
        $subtask = SubTasks::findOrFail($request->id);;

        // Update the subtask's name
        $subtask->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Subtask has been successfully updated.');
    }
    public function inputUpdate(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:inputs,id',
            // 'job_id' => 'required|exists:jobs_and_sub_tasks,id',
            'name' => [
                'required',
                Rule::unique('inputs')->ignore($request->id),
            ],
        ]);

        // Find the specific subtask within the parent job
        $input = Inputs::findOrFail($request->id);;

        // Update the subtask's name
        $input->update([
            'name' => $request->name,
        ]);

        return redirect()->back()->with('success', 'Inputs has been successfully updated.');
    }


    // manage subtasks and inputs

    public function manageSubTasks($id)
    {
        $job = Jobs::findOrFail($id);
        // return $job->subtasks;
        $existingSubTasks = $job->subtasks->pluck('id')->toArray();
        $remainingSubTasks = SubTasks::whereNotIn('id', $existingSubTasks)->get();
        return view('admin.project_management.masters.manage_subtasks', compact('job', 'remainingSubTasks'));
    }
    public function manageInputs($id)
    {
        $job = Jobs::findOrFail($id);
        // return $job->subtasks;
        $existingInputs = $job->inputs->pluck('id')->toArray();
        $remainingInputs = Inputs::whereNotIn('id', $existingInputs)->get();
        return view('admin.project_management.masters.manage_inputs', compact('job', 'remainingInputs'));
    }

    public function manageSubTasksStore(Request $request)
    {
        $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'subtasks' => 'required|array|min:1',
        ], [
            'job_id.exists' => 'The selected job does not exist.',
            'subtasks.required' => 'Please select at least one subtask.',
        ]);

        $parentJob = Jobs::findOrFail($request->job_id);

        foreach ($request->input('subtasks') as $subtaskId) {
            $parentJob->subtasks()->attach($subtaskId);
            $parentJob->increment('subtask_count');
        }


        return redirect()->back()->with('success', 'The subtask have been successfully added.');
    }
    public function manageInputsStore(Request $request)
    {
        $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'inputs' => 'required|array|min:1',
        ], [
            'job_id.exists' => 'The selected job does not exist.',
            'inputs.required' => 'Please select at least one subtask.',
        ]);

        $parentJob = Jobs::findOrFail($request->job_id);

        foreach ($request->input('inputs') as $inputId) {
            $parentJob->inputs()->attach($inputId);
            $parentJob->increment('inputs_count');
        }

        return redirect()->back()->with('success', 'The inputs have been successfully added.');
    }

    public function  manageSubTaskStatus(Request $request)
    {
        SubTasksJobs::find($request->id)->update(['is_active' => $request->status === 'true' ? true : false]);
        return response()->json(['status' => 200, 'message' => 'Status changed successfully']);
    }
    public function  manageInputStatus(Request $request)
    {
        InputsJobs::find($request->id)->update(['is_active' => $request->status === 'true' ? true : false]);
        return response()->json(['status' => 200, 'message' => 'Status changed successfully']);
    }
}
