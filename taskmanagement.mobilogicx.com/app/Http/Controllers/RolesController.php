<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Department;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data=Role::all();
        $department=Department::where('is_active',true)->get();

        return view('admin.roles',compact('data','department'));
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
            'department'=>'required',
            'name'=>['required','max:40',Rule::unique('roles')]
        ]);
        if ($request->edit_id) {
            Role::find($request->edit_id)->update(['dept_id'=>$request->department,'name'=>$request->name]);
            return back()->with('success','Updated successfully');
        }
        Role::create([
            'dept_id'=>$request->department,
            'name'=>$request->name
        ]);
        return back()->with('success','Added successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data=Role::find($id);
        return response()->json(['status'=>200,'resp'=>$data]);
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
    public function change_status(Request $request)
    {
        Role::find($request->id)->update(['is_active'=>$request->status === 'true'? true: false]);
        return response()->json(['status'=>200, 'message'=>'Status changed successfully']);
    }
}
