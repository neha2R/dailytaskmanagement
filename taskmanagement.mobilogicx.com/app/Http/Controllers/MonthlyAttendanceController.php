<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\User;

class MonthlyAttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $active_month=Carbon::create(Carbon::now());

        if (request()->has('date')) {
            $active_month = Carbon::create(request()->get('date'));
        }
        $employees = User::where('is_active',true)->where('id','!=',1)->get();

        // if (request()->get('emp_type') == 'regular') {
        //     $employees = User::where('emp_type', 'regular')->get();
        // }

        // if (request()->get('emp_type') == 'daily') {
        //     $employees = User::where('emp_type', 'daily')->get();
        // }

        // if (request()->get('emp_type') == 'monthly') {
        //     $employees = User::where('emp_type', 'monthly')->get();
        // }
        return view('admin.attendances.monthly_attendance',compact('employees','active_month'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function showMonhlyAttendance($id,$active_month)
    {
        $monthly_attendance=Attendance::where('user_id',$id)->where('date','LIKE',$active_month.'%')->get();
        return response()->json(['status'=>200,'attendance'=>$monthly_attendance]);
    }
}
