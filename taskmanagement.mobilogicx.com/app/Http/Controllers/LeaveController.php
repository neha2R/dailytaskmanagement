<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Leave;
use App\Notifications\AndroidNotification;
use Carbon\Carbon as CarbonCarbon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;

class LeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $leaves = Leave::orderBy('id', 'desc')->get();
        return view('admin.leaves', compact('leaves'));
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
        // Retrieve the leave request
        $leave = Leave::find($request->id);

        // Check if the leave request exists
        if (!$leave) {
            return response()->json(['status' => 404, 'message' => 'Leave request not found'], 404);
        }
        if (!$request->status) {
            return response()->json(['status' => 404, 'message' => 'Status not found'], 404);
        }

        // Format start date and today's date
        $startDate = Carbon::create($leave->start_date)->format('Y-m-d');
        $today = Carbon::now()->format('Y-m-d');

        // Check if approval date is over
        if ($request->status == '1' && $startDate < $today) {
            return response()->json(['status' => 201, 'message' => 'The last date for approval has passed'], 201);
        }

        // Update leave status
        $leave->update(['is_approved' => $request->status ?? "0"]);

        // Process approved leave
        if ($request->status == '1') {
            $dates = CarbonPeriod::create($leave->start_date, $leave->end_date);

            // Update or create attendance records for approved leave dates
            foreach ($dates as $date) {
                Attendance::updateOrCreate(
                    [
                        'date' => $date->format('Y-m-d'),
                        'user_id' => $leave->user_id,
                    ],
                    [
                        'user_id' => $leave->user_id,
                        'date' => $date->format('Y-m-d'),
                        'is_approved' => '4',
                        'leave_id' => $leave->id,
                        'description' => $leave->description,
                    ]
                );
            }

        }

        if ($request->status == '1') {
            // Leave request approved
            $title = 'Leave Request Approved';
            $message = 'Your leave request has been approved.';
        } elseif ($request->status == '2') {
            // Leave request rejected
            $title = 'Leave Request Rejected';
            $message = 'Your leave request has been rejected.';
        }  else {
            // Default message for other status updates
            $title = 'Leave Request Status Updated';
            $message = 'Your leave request status has been updated.';
        }

        $user = $leave->user;
        $data = [
            'notification_type' => 'leave',
            'title' => $title,
            'message' => $message,
        ];

        // Create and send the notification
        $notification = new AndroidNotification($user, $data);
        $user->notify($notification);

        return response()->json(['status' => 200, 'message' => 'Leave request status updated successfully'], 200);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Leave::find($id);
        $dep = Department::find($data->user->department_id)->name;
        return response()->json(['status' => 200, 'data' => $data, 'user' => $data->user, 'department' => $dep]);
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
}
