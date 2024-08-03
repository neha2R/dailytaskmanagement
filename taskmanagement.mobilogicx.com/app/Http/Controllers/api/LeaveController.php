<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Leave;
use App\Notifications\SendPushNotification;
use App\Notifications\WebNotification;
use Carbon\Carbon;

class LeaveController extends Controller
{
    /**
     * Apply for leave
     * 
     * @param Illuminate\Http\Request $request
     * @return response
     */
    public function apply_leave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'from' => 'required|date|after_or_equal:today',
            'to' => 'required|date|after_or_equal:from',
            'description' => 'required',
            'days' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $user = User::find($request->userid);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'No User Found.']);
        }

        $leaves = Leave::where('user_id', $request->userid)
        ->where(function ($query) use ($request) {
            $query->where('is_approved', '1')
                ->orWhere('is_approved', '0');
        })
        ->where(function ($query) use ($request) {
            $query->whereBetween('start_date', [$request->from, $request->to])
                ->orWhereBetween('end_date', [$request->from, $request->to]);
        })
        ->get();
        if ($leaves->isNotEmpty()) {
            return response()->json(['status' => false, 'message' => 'Leave dates overlap with an existing leave request.']);
        }

        $leave = new Leave;
        $leave->user_id = $request->userid;
        $leave->start_date = $request->from;
        $leave->end_date = $request->to;
        $leave->description = $request->description;
        
        // Calculate the number of days between start_date and end_date
        $startDate = new \DateTime($request->from);
        $endDate = new \DateTime($request->to);
        $days = $startDate->diff($endDate)->days + 1;
        
        $leave->days = $days;
        
        $save = $leave->save();

        if ($save) {
            $title = "New Leave Request";
            $fromDate = Carbon::createFromFormat('Y-m-d', $request->from);
            $toDate = Carbon::createFromFormat('Y-m-d', $request->to);

            $message = "{$user->name} has submitted a leave request from {$fromDate->format('d M Y')} to {$toDate->format('d M Y')}.";

            $admin = User::whereNull('role_id')->first();
            $data = [
                'title' => $title,
                'message' => $message,
            ];

            // Create and send the notification
            $notification = new SendPushNotification($title, $message, $admin, $data);
            $admin->notify($notification);

            // Web notification
            $admin->notify(new WebNotification(route('admin.leaves.index'), $title, $message));
        }

        return response()->json(['status' => true, 'message' => 'Successfully Leave Applied']);
    }


    /**
     * Apply for leave
     * 
     * @param Illuminate\Http\Request $request
     * @return response
     */
    public function leave_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $user = User::find($request->userid);
        if ($user == null) {
            return response()->json(['status' => false, 'message' => 'No User Found.']);
        }

        $leaves = Leave::where('user_id', $request->userid)->get();

        if ($leaves->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'No Leave Found.']);
        }

        $leave = [];
        $data = [];
        foreach ($leaves as $key => $value) {
            $leave['id'] = $value->id;
            $leave['from'] = date('d-M-Y', strtotime($value->start_date));
            $leave['to'] = date('d-M-Y', strtotime($value->end_date));
            $leave['status'] = $value->is_approved == '1' ? 'Approved' : ($value->is_approved == '2' ? 'Rejected' : ($value->is_approved == '3' ? 'Cancelled' : 'Pending'));
            $leave['description'] = $value->description;
            $leave['days'] = $value->days;
            $data[] = $leave;
        }

        return response()->json(['status' => true, 'data' => $data]);
    }

    /**
     * Apply for leave
     * 
     * @param Illuminate\Http\Request $request
     * @return response
     */
    public function leave_cancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
            'leaveid' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $user = User::find($request->userid);
        if ($user == null) {
            return response()->json(['status' => false, 'message' => 'No User Found.']);
        }

        $leave = Leave::where(['id' => $request->leaveid, 'user_id' => $request->userid])->first();
        if ($leave == null) {
            return response()->json(['status' => false, 'message' => 'No Leave Found.']);
        }

        $leave->is_approved = '3';
        $leave->save();
        return response()->json(['status' => true, 'message' => 'Successfully leave cancelled.']);
    }
}
