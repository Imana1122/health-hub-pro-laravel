<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    public function showChangePasswordForm(){
        return view("admin.change-password");
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
            $admin = User::where('id',Auth::user()->id)->first();
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
}
