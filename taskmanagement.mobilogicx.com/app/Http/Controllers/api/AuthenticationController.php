<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\TwoFactorAuthentication;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginVerification;


class AuthenticationController extends Controller
{
    /**
     * Trigger mail to specific user
     * 
     * @param Illuminate\Http\Request $request
     * @return response
     */
    public function triggerMail($request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        if ($user = User::where('email', $request->email)->first()) {
            if ($user->is_active == false) {
                return response()->json(['status' => false, 'message' => 'Your account is deactivated kindly contact your admin']);
            }
            $otp = $this->store_otp($user->id);
            Mail::to($user->email)->send(new LoginVerification($user->email, $otp, $user->name));
            return response()->json(['status' => true, 'message' => "Please check your mail for otp"]);
        } else {
            return response()->json(['status' => false, 'message' => "Email is not registered"]);
        }
    }

    /**
     * Specific user Login
     * 
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response 
     */
    public function login(Request $request)
    {
        return $this->triggerMail($request);
    }

    /**
     * resend otp to specific user
     * 
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response 
     */
    public function resend_otp(Request $request)
    {

        return $this->triggerMail($request);
    }

    /**
     * generate otp and store to database
     * 
     * @param int $userid
     * @return int $otp
     */
    public function store_otp($userid)
    {
        $recentOTP = TwoFactorAuthentication::where(['user_id' => $userid])->whereNull('is_expired')->get();
        if ($recentOTP->isNotEmpty()) {
            foreach ($recentOTP as $key => $value) {
                $value->is_expired = now();
                $value->save();
            }
        }
        $otp = rand(1111, 9999);
        TwoFactorAuthentication::create(['user_id' => $userid, 'otp' => $otp]);
        return $otp;
    }

    /**
     * Verifying specific user's OTP
     * 
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response 
     */
    public function verify_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|numeric|min:4',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first(), 'data' => []]);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'invalid email address', 'data' => []]);
        }
        $isValid = TwoFactorAuthentication::where(['otp' => $request->otp, 'user_id' => $user->id])->whereNull('is_expired')->first();
        if ($isValid) {
            if ($user->is_active == false) {
                return response()->json(['status' => false, 'message' => 'Your account is deactivated kindly contact your admin', 'data' => []]);
            }
            $isValid->is_expired = now();
            $isValid->save();

            $post =  $user->role ? $user->role->name : '';
            $result = ['userid' => $user->id, 'name' => $user->name, 'post' => $post, 'profile_photo_path' => $user->profile_photo_path ? asset('storage') . '/' . $user->profile_photo_path : ''];
            return response()->json(['status' => true, 'message' => 'success', 'data' => $result, 'token' => $user->createToken("API TOKEN")->plainTextToken]);
        } else {
            return response()->json(['status' => false, 'message' => 'invalid otp', 'data' => []]);
        }
    }

    /**
     * store fcm token
     * 
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response 
     */
    // public function send_fcm_token(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'userid' => 'required',
    //         'token' => 'required',
    //         'device_id' => 'required'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
    //     }

    //     if (User::whereId($request->userid)->first()) {
    //         if ($deviceToken = DeviceToken::where('user_id', $request->userid)->first()) {
    //             $deviceToken->token = $request->token;
    //             $deviceToken->device_id = isset($request->device_id) ? $request->device_id : null;
    //             $deviceToken->save();

    //             return response()->json(['status' => true, 'message' => 'success']);
    //         } else {
    //             $deviceToken = new DeviceToken;
    //             $deviceToken->user_id = $request->userid;
    //             $deviceToken->token = $request->token;
    //             $deviceToken->device_id = isset($request->device_id) ? $request->device_id : '';
    //             $deviceToken->save();

    //             return response()->json(['status' => true, 'message' => 'success']);
    //         }
    //     } else {
    //         return response()->json(['status' => false, 'message' => 'No User Found.']);
    //     }
    // }


    function privacypolicy()
    {
        return response()->json(['status' => 200, 'url' => route('privacy-policy')]);
    }
}
