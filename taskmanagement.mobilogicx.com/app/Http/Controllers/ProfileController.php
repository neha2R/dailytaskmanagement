<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user=Auth::user();
        return view('admin.profile',compact('user'));
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
        $request->validate(['image'=>'required']);
        $user=User::find(Auth::user()->id);
        if ($request->image) {
            deleteOldImage($user->profile_photo_path);
            $image=$request->file('image')->store('user_profile','public');
            $user->update(['profile_photo_path'=>$image]);
            return redirect()->back()->with('message','Photo updated successfully');
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
        $user=User::find($id);
        if ($request->name)  {
            $request->validate([
                'name' => 'required',
                'email' => ['required','email', Rule::unique('users')->ignore(Auth::user()->id)],
                'mobile' => ['required', Rule::unique('users')->ignore(Auth::user()->id)],
            ]);
            $update=$user->update(['name'=>$request->name,'email'=>$request->email,'mobile'=>$request->mobile]);
            return redirect()->back()->with('success','Profile updated successfully');
        }
        if ($request->current_password) {
            $this->validate($request, [
                'current_password' => 'required|string',
                'new_password' => 'required|confirmed|min:8|string'
            ]);
            $auth = Auth::user();
     
     // The passwords matches
            if (!Hash::check($request->get('current_password'), $auth->password)) 
            {
                return back()->with('error', "Current Password is Invalid");
            }
     
    // Current password and new password same
            if (strcmp($request->get('current_password'), $request->new_password) == 0) 
            {
                return redirect()->back()->with("error", "New Password cannot be same as your current password.");
            }
     
            $user =  User::find($auth->id);
            $user->password =  Hash::make($request->new_password);
            $user->save();
            return back()->with('success', "Password Changed Successfully");

        }
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
