<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $active_date = Carbon::create(Carbon::now());

        if (request()->has('date')) {
            $active_date = Carbon::create(request()->get('date'));
        }

        $employees = User::where('is_active',true)->where('id','!=',1)->with('attendance', function ($query) use ($active_date) {
            $query->where('date', dateformat($active_date, 'Y-m-d'));
        })->get();


        // if (request()->get('emp_type') == 'regular') {
        //     $employees = User::with('attendance', function ($query) use ($active_date) {
        //         $query->where('date', dateformat($active_date, 'Y-m-d'));
        //     })->get();
        // }

        // if (request()->get('emp_type') == 'daily') {
        //     $employees = User::with('attendance', function ($query) use ($active_date) {
        //         $query->where('date', dateformat($active_date, 'Y-m-d'));
        //     })->get();
        // }

        // if (request()->get('emp_type') == 'monthly') {
        //     $employees = User::with('attendance', function ($query) use ($active_date) {
        //         $query->where('date', dateformat($active_date, 'Y-m-d'));
        //     })->get();
        // }

        return view('admin.attendances.attendance', compact('employees', 'active_date'));
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
        $date = dateFormat($request->date, 'Y-m-d');
        if ($request->type == 'P') {
            foreach ($request->check as $user_id => $val) {
                Attendance::updateOrCreate(
                    [
                        'user_id' => $user_id,
                        'date' => $date
                    ],
                    [
                        'is_approved' => '1'
                    ]
                );
            }
            return redirect()->back()->with('success', 'Attendance present marked successfully');
        }
        if ($request->type == 'HD') {
            foreach ($request->check as $user_id => $val) {
                Attendance::updateOrCreate([
                    'user_id' => $user_id,
                    'date' => $date
                ], [
                    'is_approved' => '3'
                ]);
            }
            return redirect()->back()->with('success', 'Attendance half day marked successfully');
        }
        if ($request->type == 'A') {
            foreach ($request->check as $user_id => $val) {
                Attendance::updateOrCreate([
                    'user_id' => $user_id,
                    'date' => $date
                ], [
                    'is_approved' => '2'
                ]);
            }
            return redirect()->back()->with('success', 'Attendance absent marked successfully');
        }
        return redirect()->back()->with('error', 'Somthing went worng');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

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
    public function showEmp($id , $active_date)
    {
        // $active_date=dateformat($active_date,'Y-m-d');
        $employee = User::where('id',$id)->with('attendance', function ($query) use ($active_date) {
            $query->where('date', dateformat($active_date, 'Y-m-d'));
        })->first();
        return response()->json(['status'=>200,'employee'=>$employee]);
    }
}
