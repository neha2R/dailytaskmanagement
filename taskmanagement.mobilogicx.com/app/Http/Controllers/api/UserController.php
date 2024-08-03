<?php

namespace App\Http\Controllers\api;

use App\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{

    /**
     * show specific user profile
     * 
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response 
     */
    public function get_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first(), 'data' => []]);
        }

        if ($user = User::find($request->userid)) {
            $department = $user->department ? $user->department->name : '';
            $role = $user->role ? $user->role->name : '';
            $staff = $user->emp_type ?? '';
            $data = [
                'name' => ucwords($user->name),
                'mobile' => $user->mobile,
                'email' => $user->email,
                'profile_photo_path' => $user->profile_photo_path ? asset('storage') . '/' . $user->profile_photo_path : '',
                'dept' => $department, 'role' => $role,
                'staff_type' => $staff,
            ];
            return response()->json(['status' => true, 'message' => 'success', 'data' => $data]);
        } else {
            return response()->json(['status' => false, 'message' => "No user found.", 'data' => []]);
        }
    }


    /**
     * Get all department details
     * 
     * @param void
     * @return \Illuminate\Http\Response 
     */
    public function get_department()
    {
        $data = Department::select('id', 'name')->where('is_active', '1')->get()->toArray();
        if (count($data)) {
            return response()->json(['status' => true, 'message' => 'success', 'data' => $data]);
        } else {
            return response()->json(['status' => false, 'message' => 'no data found', 'data' => []]);
        }
    }

    /**
     * Get all Roles details
     * 
     * @param void
     * @return \Illuminate\Http\Response 
     */
    public function get_role()
    {
        $data = Role::select('id', 'name')->where('is_active', '1')->get()->toArray();
        if (count($data)) {
            return response()->json(['status' => true, 'message' => 'success', 'data' => $data]);
        } else {
            return response()->json(['status' => false, 'message' => 'no data found', 'data' => []]);
        }
    }

    /**
     * Get all staff's details
     * 
     * @param void
     * @return \Illuminate\Http\Response 
     */

    public function update_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required|numeric',
            'name' => 'required|regex:/^[\pL\s\-]+$/u|min:3|max:30',
            'email' => 'required|email|unique:users,email,' . $request->userid,
            'mobile' => 'required|digits:10|unique:users,mobile,' . $request->userid,
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        if ($user = User::find($request->userid)) {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile = $request->mobile;
            $user->save();
            $data['id'] = $user->id;
            $data['name'] = $user->name;
            $data['email'] = $user->email;
            $data['mobile'] = $user->mobile;
            return response()->json(['status' => true, 'message' => "Successfully profile updated.", 'data' => $data]);
        } else {
            return response()->json(['status' => false, 'message' => "No user found."]);
        }
    }

    function check_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userid' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }
        $user = User::find($request->userid);
        if ($user) {
            $data = [];
            $data['name'] = $user->name;
            $data['status'] = $user->is_active;
            return response()->json(['status' => true, 'message' => "Success", 'data' => $data]);
        } else {
            return response()->json(['status' => false, 'message' => "User not found",]);
        }
    }
    public function updateFcmToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric|exists:users,id',
            'token' => [
                'required',
                'string'
            ]
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->first());
        }

        $user = User::find($request->user_id);

        if (!$user) {
            return ApiResponse::validationError("User not found");
        }

        $user->update(['device_token' => $request->token]);

        $data = [
            'user_id' => $user->id,
            'name' => $user->name,
            'token' => $user->device_token,
        ];

        return ApiResponse::success($data, 'Token updated successfully');
    }

    /**
     * get all state details
     * 
     * @param void
     * @return \Illuminate\Http\Response 
     */
    //send mail for forget password
    // public function forget_password(Request $req)
    // {
    //     $validator = Validator::make($req->all(), [
    //         'email' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => false, 'message' => $validator->errors()]);
    //     }

    //     if ($user = User::where('email', $req->email)->first()) {
    //         $data = ['user' => $user->id];
    //         $forgetPassword = new ForgetPassword;
    //         $forgetPassword->user_id = $user->id;
    //         $forgetPassword->save();
    //         Mail::to($req->email)->send(new \App\Mail\ForgetPassword($data));

    //         return response()->json(['status' => true, 'message' => "Email has been sent successfully. please check you email."]);
    //     } else {
    //         return response()->json(['status' => false, 'message' => "Email does not match to any user."]);
    //     }
    // }

    //update form open
    // public function update_password($id)
    // {

    //     if (ForgetPassword::where('user_id', Crypt::Decrypt($id))->whereDate('created_at', carbon::now())->first()) {
    //         $token = $id;
    //         return view('auth.update_password_web', compact('token'));
    //     } else {

    //         $error = "Link Has Been Expired. please Generate Email again";
    //         return redirect('/')->with('error', $error);
    //     }
    // }

    //update password of a user
    // public function password_update(Request $req)
    // {

    //     if ($req->password_confirmation != $req->password) {
    //         return redirect()->back()->with('error', 'Your password doesnot match. Try again!');
    //     } else {
    //         $user = ForgetPassword::where('user_id', Crypt::Decrypt($req->token))->whereDate('created_at', carbon::now())->first();
    //         if ($user) {
    //             ForgetPassword::where('user_id', Crypt::Decrypt($req->token))->whereDate('created_at', carbon::now())->delete();

    //             $data = User::whereId(Crypt::Decrypt($req->token))->first();
    //             $data->password = bcrypt($req->password);
    //             $data->save();
    //             return redirect('/')->with('success', "Password Has Been updated successfully.");
    //         } else {
    //             return redirect()->back()->with('error', 'Sorry change password link has been expired. Try again1!');
    //         }
    //     }
    // }
}
