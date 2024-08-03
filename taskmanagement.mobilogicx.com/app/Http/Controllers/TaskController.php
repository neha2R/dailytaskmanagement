<?php

namespace App\Http\Controllers;

use App\Models\ManageTask;
use App\Models\User;
use App\Models\Vehicles;
use App\Models\VehicleUser;
use App\Models\ManageTaskProcess;
use App\Models\WhDpMapedUser;
use App\Models\WhDpMappedVehicles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\Department;


class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            $department=Department::where('is_active',true)->get();

            $users = User::where('is_active',true)->where('id', '!=', 1)->get();
            $taskdata = ManageTask::orderBy('id', 'desc')->get();
            $roles=Role::where('is_active',true)->get();


            return view('admin.task_management.task_management', compact('users','taskdata','roles','department'));
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
        $date= Carbon::today()->toDateString();
        $validatedData = $request->validate([
            'user_id' => 'required',
            'emp_type' => 'required',
            'role_id' => 'required',
            'startdate' => 'required|date',
            'enddate' => 'required|date|after:startdate',
            'task_name' => 'required',
            'priority' => 'required',
          
        ]);

        try {
         //// dd($request['startdate']);
        //    $user = Auth::user();

            // Start a database transaction
          ////  DB::beginTransaction();
            // Use the create method to save the data
            $task = ManageTask::create([
                'name' => $request->task_name ?? null,
                'description' => $request->description ?? null,
                'startdate' => dateformat($request['startdate'], 'Y-m-d'),
                'enddate' => dateformat($request['enddate'], 'Y-m-d'),
                'dept_id' => $request['emp_type'],
                'user_id' => $request['user_id'],
                'role_id' => $request['role_id'],
                'priority' => $request['priority'],
                'status' => (dateformat($request['startdate'], 'Y-m-d') > $date ? "upcoming" : "to-do"),
                
            ]);

           
            // Commit the transaction if everything is successful
            DB::commit();

            // Redirect back with a success message
            return redirect()->back()->with('success', 'Data saved successfully');
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollback();
            return $e;
            // Redirect back with an error message
            return redirect()->back()->with('error', 'Failed to save data. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show1($id)
    {
        $data = ManageTask::with('user')->find($id);
        $data->startdate = dateformat($data->startdate, 'd M Y');
        $data->enddate = dateformat($data->enddate, 'd M Y');
        $roles1=Role::where('is_active',true)->where('dept_id' , $data->dept_id)->get();
        $users=User::where('is_active',true)->where('role_id' , $data->role_id)->get();

        return response()->json(['status' => 200, 'data' => $data, 'roles1' => $roles1 , 'users' => $users]);
    }
       public function show($id)
    {
        $data = ManageTask::with('user')->find($id);
        $data->startdate = dateformat($data->startdate, 'd M Y');
        $data->enddate = dateformat($data->enddate, 'd M Y');
        $taskprogress = ManageTaskProcess::where('task_id', $id)->get();

        return view('admin.task_management.task_detail', compact('data','taskprogress'));

       ///// return response()->json(['status' => 200, 'data' => $data]);
    }
      public function getUsers($id)
    {
        $users = User::where('is_active', 1)->where('role_id', $id)->get();
        return response()->json(['status' => 200, 'users' => $users]);
    }

    public function prioritylevel($id)
    {
        //dd($id);
        $users = User::where('is_active',true)->where('id', '!=', 1)->get();
        $taskdata = ManageTask::where('is_active',true)->where('priority',1)->get();


        return view('admin.task_management.task_management', compact('users','taskdata'));
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
        $date= Carbon::today()->toDateString();

          $request->validate([
            'user_id' => 'required',
            'emp_type' => 'required',
            'role_id' => 'required',
            'startdate' => 'required|date',
            'enddate' => 'required|date|after:startdate',
            'task_name' => 'required',
            'priority' => 'required',
        ]);
        $depo=ManageTask::find($request->id);
        $depo->update([
            'name'=>$request->task_name,
            'description'=>$request->description,
            'startdate' => dateformat($request['startdate'], 'Y-m-d'),
            'enddate' => dateformat($request['enddate'], 'Y-m-d'),
            'user_id' => $request['user_id'],
            'role_id' => $request['role_id'],
            'dept_id' => $request['emp_type'],
            'priority' => $request['priority'],
            'status' => (dateformat($request['startdate'], 'Y-m-d') > $date ? "upcoming" : "to-do"),

        ]);
        return redirect()->back()->with('success','Task updated successfully');
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
     public function change_status(Request $request)
    {
        $tasklog=ManageTaskProcess::where('task_id',$request->id)->first();
        if(!$tasklog)
        {
        ManageTask::find($request->id)->update(['is_active' => $request->status === 'true' ? true : false]);
         if($request->status=="false")
         {
         ManageTask::find($request->id)->update(['status' => "cancelled"]);
   
         }
        return response()->json(['status' => 200, 'message' => 'Status changed successfully']);
        }
        else
        {

        return response()->json(['status' => 202, 'message' => 'The Task is already scheduled']);    
        }
    }
}
