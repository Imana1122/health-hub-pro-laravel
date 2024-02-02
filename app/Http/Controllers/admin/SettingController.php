<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function showChangePasswordForm(){
        return view("admin.account.change-password");
    }

    public function changePassword(Request $request){
        $validator = Validator::make($request->all(), [
            "password"=> "required|confirmed|min:5",
            'old_password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'errors'=> $validator->errors(),
                'status' => false
            ]);
        }else{
            $admin = Admin::where('id', Auth::guard('admin')->id())->first();
            if(!Hash::check($request->old_password, $admin->password)){

                $message = 'Your old password is incorrect. Try again.';
                session()->flash('error', $message);

                return response()->json([
                    'status' => true,
                    'message'=> $message

                ]);
            }else{
                $admin->password = bcrypt($request->password);
                $admin->save();

                $message = 'Your password is updated successfully';
                session()->flash('success', $message);

                return response()->json([
                    'status'=> true,
                    'message'=> $message
                ]);
            }
        }
    }

    public function profile(){

        $user = Admin::where('id', Auth::guard('admin')->id())->first();


        return view('admin.account.profile',compact('user'));
    }


    public function updateProfile(Request $request){
        $adminId = Auth::guard('admin')->id();

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'phone_number' => 'required|unique:admins,phone_number,' . $adminId . ',id',
        ]);

        if($validator->passes()){

            $admin = Admin::find($adminId);
            $admin->name = $request->name;
            $admin->phone_number = $request->phone_number;
            $admin->save();

            $message = 'Profile updated successfully';

            session()->flash('success', $message);
            return response()->json([
                'status' => true,
                'message' => $message
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }


    }

    public function deleteAccount(Request $request)
    {
        $admin = Auth::guard('admin');

        // Validate the incoming request
        $request->validate([
            'password' => 'required|string',
        ]);

        // Check if the provided password matches the admin's current password
        if (Hash::check($request->password, $admin->password)) {
            $admin = Admin::find($admin->id);
            // Delete the admin
            $admin->delete();

            // Logout the admin
            Auth::logout();

            // Redirect to a confirmation page or any other page
            return redirect()->route('admin.login')->with('success', 'Your account has been deleted successfully.');
        } else {
            // Password doesn't match
            return redirect()->back()->with('error', 'Incorrect password. Please try again.');
        }
    }
}
