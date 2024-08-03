<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Position;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $department=Department::where('is_active',true)->get();
        $position=Position::where('is_active',true)->get();
        $roles=Role::where('is_active',true)->get();
        $data=User::where('role_id','!=',null)->get();
        return view('admin.employees',compact('data','department','position','roles'));
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
  public function getRoles($id)
    {
        $roles = Role::where('is_active', 1)->where('dept_id', $id)->get();
        return response()->json(['status' => 200, 'roles' => $roles]);
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
            'name'=>'required',
            'email'=>'email|unique:users,email|required',
            'mobile'=>'required|unique:users,mobile',
            // 'emp_type'=>'required',
            'gender'=>'required',
            'image'=>'',
            'department'=>'required',
            'role_id'=>'required',
            // 'position'=>'required',
        ]);
        $image='';
        if ($request->image) {
            $image=$request->file('image')->store('user_profile','public');
        }
        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'mobile'=>$request->mobile,
            'emp_type'=>$request->emp_type,
            'gender'=>$request->gender,
            'profile_photo_path'=>$image,
            'department_id'=>$request->department,
            'role_id'=>$request->role_id,
            'position_id'=>$request->position,
            'password'=>Hash::make($request->mobile),
        ]);
        if ($user) {
            return redirect()->route('admin.employees.index')->with('success','Employee created successfully');
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
        $data=User::find($id);
        $roles1=Role::where('is_active',true)->where('dept_id' , $data->department_id)->get();

        return response()->json(['status'=>200,'resp'=>$data,'roles1'=>$roles1]);
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
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users,email'. ($request->id ? ",$request->id " : ''),
            'mobile'=>'required|unique:users,mobile'. ($request->id  ? ",$request->id " : ''),
            // 'emp_type'=>'required',
            'gender'=>'required',
            'updateImage'=>'',
            'department'=>'required',
            'role_id'=>'required',
            // 'position'=>'required',
        ]);
        $user=User::find($request->id);
        if ($request->updateImage) {
            deleteOldImage($user->profile_photo_path);
            $image=$request->file('updateImage')->store('user_profile','public');
            $update=$user->update(['profile_photo_path'=>$image]);
        }
        $user->update([
            'name'=>$request->name,
            'email'=>$request->email,
            'mobile'=>$request->mobile,
            'emp_type'=>$request->emp_type,
            'gender'=>$request->gender,
            'department_id'=>$request->department,
            'role_id'=>$request->role_id,
            'position_id'=>$request->position,
        ]);
        return redirect()->back()->with('success','Updated successfully');
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
        User::find($request->id)->update(['is_active'=>$request->status === 'true'? true: false]);
        return response()->json(['status'=>200, 'message'=>'Status changed successfully']);
    }
}
