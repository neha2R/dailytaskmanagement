<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Carbon;
use App\Models\ManageTask;
use App\Models\ManageTaskProcess;
class AttendanceController extends Controller
{
    /**
     * Attendance mangement
     * 
     * @param Illuminate\Http\Request $request
     * @return response
     */
    public function mark_attendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required|numeric|min:1',
            // 'date' => 'required|date',
            // 'time' => 'required',
            'lat' => 'required',
            'long' => 'required',
            // 'city' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        if ($request->lat == '0.0' || $request->long == '0.0') {
            return response()->json(['status' => false, 'message' => 'Latituede and Longitude is not valid']);
        }

        // if ($request->date != now()->format('Y-m-d')) {
        //     return response()->json(['status' => false, 'message' => 'Attendance date can not be greater then or less then today.']);
        // }

        $date = Carbon::now()->format('Y-m-d');
        $time = Carbon::now()->format('H:i:s');
        // return $date;
        $user = User::find($request->userid);
        if ($user == null) {
            return response()->json(['status' => false, 'message' => 'no user found...']);
        }
        // check login time for attendance remark
        if (Attendance::where('user_id', $request->userid)->where('date', $date)->where('login_time', '!=', null)->first()) {
            return response()->json(['status' => true, 'message' => 'You have already added the attendance to the requesting date.']);
        }

        $attendance = new Attendance;
        $attendance->user_id = $request->userid;
        $attendance->date = $date;
        $attendance->login_time = $time;
        $attendance->login_latitude = $request->lat;
        $attendance->login_longitude = $request->long;
        //   $attendance->login_location_name = $request->city;
        $attendance->save();
        return response()->json(['status' => true, "loginstatus" => "1", 'message' => 'Attendance mark-in successfully']);
    }
    public function markoutattendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required|numeric|min:1',
            'date' => 'required|date',
            'time' => 'required',
            'lat' => 'required',
            'long' => 'required',
            // 'city' => 'required|numeric|min:1',
        ]);
       
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        
        $date=Carbon::today()->toDateString();
         $tasks = ManageTask::where('user_id', $request->userid)->whereDate('startdate', $date)->where('status', 'to-do')->get();
        if($tasks->isNotEmpty())
        {
           // dd($tasks);
           $data= $tasks->map(function ($task) {
                $taskprogress=ManageTaskProcess::where('task_id',$task->id)->whereNotNull('progress_report')->latest()->first();
             ////   dd($taskprogress);
                if(!$taskprogress)
                {
                  return 1;
                }
            });
             if(in_array(1, $data->toArray()))
             
             {
        return response()->json(['status' => false, 'message' => 'Please Update your Today\'s Task progress.']);
             }
        }
        $tasks2 = ManageTask::where('user_id', $request->userid)->where('status', 'in-process')->get();
        if($tasks2->isNotEmpty())
        {
           // dd($tasks);
           $data2= $tasks2->map(function ($task2) {
               $date=Carbon::today()->toDateString();
                $taskprogress2=ManageTaskProcess::where('task_id',$task2->id)->whereDate('start_date', $date)->whereNotNull('progress_report')->latest()->first();
          //dd($taskprogress2);
                if(!$taskprogress2)
                {
                  return 1;
                }
            });
            if(in_array(1, $data2->toArray()))
             {
        return response()->json(['status' => false, 'message' => 'Please Update your Today\'s Task progress.']);
             }
        }
      
        if ($request->lat == '0.0' || $request->long == '0.0') {
            return response()->json(['status' => false, 'message' => 'Latituede and Longitude is not valid']);
        }

        if ($request->date != now()->format('Y-m-d')) {
            return response()->json(['status' => false, 'message' => 'Attendance date can not be greater then or less then today.']);
        }
        $date = Carbon::now()->format('Y-m-d');
        $time = Carbon::now()->format('H:i:s');
        $user = User::find($request->userid);
        if ($user == null) {
            return response()->json(['status' => false, 'message' => 'no user found...']);
        }

        if (Attendance::where('user_id', $request->userid)->where('date', $date)->where('logout_time', '!=', null)->first()) {
            return response()->json(['status' => true, 'message' => 'You have already added the attendance to the requesting date.']);
        }
        $attendance = Attendance::where('user_id', $request->userid)->where('date', now()->format('Y-m-d'))->where('login_time', '!=', null)->latest()->first();
        // $attendance = new Attendance;
        $attendance->user_id = $request->userid;
        // $attendance->date = $request->date;
        $attendance->logout_time = $time;
        $attendance->logout_latitude = $request->lat;
        $attendance->logout_longitude = $request->long;
        $attendance->logout_location_name = $request->city;
        $attendance->save();
        return response()->json(['status' => true, "loginstatus" => "2", 'message' => 'Attendance mark-out successfully']);
    }

    /**
     * Get specific user's all attendance
     * 
     * @param Illuminate\Http\Request $request
     * @return response
     */
    public function get_attendance(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'userid' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first(), 'data' => []]);
        }

        $user = User::find($req->userid);

        if ($user == null) {
            return response()->json(['status' => false, 'message' => 'No User Found.', 'data' => []]);
        }
        if ($req->date) {
            $start_date = Carbon::parse($req->date)->startOfMonth()->toDateString();

            $end_date = Carbon::parse($req->date)->endOfMonth()->toDateString();
        } else {

            $start_date = Carbon::now()->startOfMonth()->toDateString();
            $end_date = Carbon::now()->endOfMonth()->toDateString();
        }
        $res = [];
        $data = [];
        $presentdays = 0;
        $attendance = Attendance::where('user_id', $req->userid)->whereDate('date', '>=', $start_date)->whereDate('date', '<=', $end_date)->get();
        if ($attendance->isEmpty()) {

            $res['previous_days'] =[] ;

            $res['current_date'] = now()->format('Y-m-d');
            $res['current_time'] = now()->format('H:i:s');
            $res['total_attendance'] = (string) $presentdays;
            $res['current_month'] = date('F');
            

            return response()->json(['true' => true, 'message' => 'success', 'data' => $res]);
        }

        // $data['previous_days'];

        // 0=pending, 1=accept(present), 2=reject(abcent), 3=half day, 4=leave

        foreach ($attendance as $key => $value) {
            $data['date'] = $value->date;
            if ($value->is_approved > 2) {

                $status = $value->is_approved == '3' ? 'halfday' : 'leave';
            } else {
                $status = $value->is_approved == '2' ? 'absent' : ($value->is_approved == '1' ? 'approved' : 'pending');
            }
            if ($value->is_approved == 3) {
                $presentdays = $presentdays + .5;
            }
            if ($value->is_approved == 1) {
                $presentdays = $presentdays + 1;
            }
            $data['status'] = $status;
            $data['time'] = $value->login_time;
            $res['previous_days'][] = $data;
        }
        $res['current_date'] = now()->format('Y-m-d');
        $res['current_time'] = now()->format('H:i:s');
        $res['total_attendance'] = (string) $presentdays;;
        // $attendance->count();
        $res['current_month'] = date('F');
        return response()->json(['true' => true, 'message' => 'success', 'data' => $res]);
    }


    public function checkLoginAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required|numeric|min:1',
            // 'date' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        if ($request->lat == '0.0' || $request->long == '0.0') {
            return response()->json(['status' => false, 'message' => 'Latituede and Longitude is not valid']);
        }

        // if ($request->date != now()->format('Y-m-d')) {
        //   return response()->json(['status' => false, 'message' => 'Attendance date can not be greater then or less then today.']);
        // }

        $user = User::find($request->userid);
        $date = Carbon::now()->format('Y-m-d');
        // return $date;
        if ($user == null) {
            return response()->json(['status' => false, 'message' => 'no user found...']);
        }
        $ATten = Attendance::where('user_id', $request->userid)->where('date', $date)->first();
        // return now()->format('Y-m-d');
        if ($ATten) {
            if ($ATten->login_time != null && $ATten->logout_time != null) {
                return response()->json(['status' => true, 'loginstatus' => '2', 'message' => 'You have already added the attendance to the requesting date.']);
            } elseif ($ATten->login_time != null && $ATten->logout_time == null) {
                return response()->json(['status' => true, 'loginstatus' => '1', 'message' => 'You have already added the attendance to the requesting date.']);
            }
            // return response()->json(['status'=>true,'message'=>'admin already marked your attendance']);
        } else {
            return response()->json(['status' => true, 'loginstatus' => '0', 'message' => 'Attendence not mark yet.']);
        }
        if ($request->type == 1) {
            if (Attendance::where('user_id', $request->userid)->where('date', now()->format('Y-m-d'))->where('login_time', '!=', null)->first()) {
                return response()->json(['status' => true, 'loginstatus' => '1', 'message' => 'You have already added the attendance to the requesting date.']);
            } else {
                return response()->json(['status' => false,  'loginstatus' => '0', 'message' => 'Attendence not mark yet.']);
            }
        }
        if ($request->type == 2) {
            if (Attendance::where('user_id', $request->userid)->where('date', now()->format('Y-m-d'))->where('logout_time', '!=', null)->first()) {
                return response()->json(['status' => true,  'loginstatus' => '2', 'message' => 'You have already added the attendance to the requesting date.']);
            } else {
                return response()->json(['status' => false,   'loginstatus' => '0', 'message' => 'Attendence not mark yet.']);
            }
        }
    }

    public function deleteAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required|numeric|min:1',
            'date' => 'required',
            // 'type' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $user = User::find($request->userid);
        if ($user == null) {
            return response()->json(['status' => false, 'message' => 'no user found...']);
        }
        // if ($request->type == "1") {
        $data = Attendance::where('user_id', $request->userid)->where('date', $request->date)->first();
        if ($data) {
            $data = $data->delete();
            return response()->json(['status' => 200, 'message' => 'deleted successfully']);
        } else {
            return response()->json(['status' => 400, 'message' => 'attendance not found']);
        }
        // }
        // if ($request->type == "2") {
        //     $data=Attendance::where('user_id',$request->userid)->where('date',$request->date)->whereNotNull('logout_time')->first();
        //     if ($data) {
        //         $data=$data->delete();
        //         return response()->json(['status'=>200,'message'=>'deleted successfully']);
        //     }else{
        //         return response()->json(['status'=>400,'message'=>'attendance not found']);
        //     }
        // }
    }
}
