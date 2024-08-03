<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $senior_position = Position::where('name', 'Like', '%senior%')->first();
        $seniors = User::where('position_id', $senior_position ? $senior_position->id : 1)->orderBy('name', 'asc')->get();
        $departments = Department::where('is_active', 1)->get();
        return view('admin.assignment', compact('departments', 'seniors'));
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
        $request->validate([
            'department' => 'required',
            'senior' => 'required',
            'juniors' => 'required'
        ]);
        $update = User::whereIn('id',$request->juniors)->update(['senior_id'=> $request->senior]);

        return redirect()->back()->with('success', 'Juniors assigned successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    { //use this function to show juniors 
        $user = User::find($id);
        if ($user) {
            $junior_position = Position::where('name', 'Like', '%junior%')->first();
            $juniors = User::where('is_active',true)->where('position_id', $junior_position->id)->where('department_id', $user->department_id)->where('senior_id', null)->get();
            return response()->json(['status' => 200, 'juniors' => $juniors]);
        }
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
        $update=User::find($id)->update(['senior_id'=>null]);
        return response()->json(['status'=>200,'message'=>'Removed successfully']);
    }
    public function getSeniors($id)
    {
        $senior_position = Position::where('name', 'Like', '%senior%')->first();
        $seniors = User::where('is_active', 1)->where('position_id', $senior_position->id)->where('department_id', $id)->get();
        return response()->json(['status' => 200, 'seniors' => $seniors]);
    }
    public function showAssignement($id)
    {
        $users=User::where('is_active',1)->where('senior_id',$id)->with('position')->get();
        return response()->json(['status' => 200, 'users' => $users]);
    }
}
