<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index(Request $request){
        $admins = Admin::latest('created_at');

        if(!empty($request->get('keyword'))){
            $admins = $admins->where('name','like','%'.$request->get('keyword'). '%')
                    ->orwhere('phone_number','like','%'.$request->get('keyword'). '%');

        }

        $admins = $admins->paginate(10);

        return view('admin.admins.list', compact('admins'));

    }

    public function create(Request $request){
        $authUser = Auth::guard('admin')->user();
        if($authUser->role !== '1'){
            $message = 'Your are not authorized to create admins.';
            session()->flash('error', $message);
            return redirect()->route('admins.index');
        }

        return view('admin.admins.create');

    }

    public function store(Request $request){
        $authUser = Auth::guard('admin')->user();
        if($authUser->role !== '1'){
            $message = 'Your are not authorized to add admins.';
            session()->flash('error', $message);
            return redirect()->route('admins.index');
        }

        $validator = Validator::make($request->all(), [
            'name'=> 'required|min:3',
            'phone_number'=> 'required|unique:admins,phone_number',
            'password' => 'required|confirmed|min:5'
        ]);

        if($validator->passes()){

            $admin = new Admin();
            $admin->name = $request->name;
            $admin->phone_number = $request->phone_number;
            $admin->password = $request->password;
            $admin->save();

            $message = 'Admin added successfully';
            session()->flash('success', $message);

            return response()->json([
                'errors'=> $message,
                'status' =>true
            ]);


        }else{
            return response()->json([
                'errors'=> $validator->errors(),
                'status' =>false
            ]);
        }
    }

    public function edit($id, Request $request){
        $authUser = Auth::guard('admin')->user();
        if($authUser->role !== '1'){
            $message = 'Your are not authorized to edit admins.';
            session()->flash('error', $message);
            return redirect()->route('admins.index');
        }
        $admin = Admin::find($id);
        if($admin == null){
            $message = 'Admin not found';
            session()->flash('error', $message);
            return redirect()->route('admins.index');
        }

        return view('admin.admins.edit', compact('admin'));

    }

    public function update(Request $request, $id){
        $authUser = Auth::guard('admin')->user();
        if($authUser->role !== '1'){
            $message = 'Your are not authorized to update admins.';
            session()->flash('error', $message);
            return redirect()->route('admins.index');
        }
        $admin = Admin::find($id);

        if($admin == null){
            $message = 'Admin not found';
            session()->flash('error', $message);
            return redirect()->route('admins.index');
        }

        $validator = Validator::make($request->all(), [
            'name'=> 'required|min:3',
            'phone_number'=> 'required|unique:admins,phone_number,'. $id . ',id',
            'password' => 'nullable|confirmed|min:5'
        ]);

        if($validator->passes()){

            $admin->name = $request->name;
            $admin->phone_number = $request->phone_number;
            $admin->status = $request->status;
            $admin->role = $request->role;
            $admin->save();

            $message = 'Admin updated successfully';
            session()->flash('success', $message);

            return response()->json([
                'errors'=> $message,
                'status' =>true
            ]);


        }else{
            return response()->json([
                'errors'=> $validator->errors(),
                'status' =>false
            ]);
        }
    }

    public function destroy($id){
        $authUser = Auth::guard('admin')->user();
        if($authUser->role !== '1'){
            $message = 'Your are not authorized to delete admins.';
            session()->flash('error', $message);
            return redirect()->route('admins.index');
        }
        $admin = Admin::find($id);

        if($admin == null){
            $message = 'Admin not found';
            session()->flash('error', $message);
            return redirect()->route('admins.index');
        }
        $message = 'Admin deleted successfully';
        $admin->delete();
        session()->flash('success', $message);
        return response()->json([
            'status'=>true,
            'message'=> $message
        ]);
    }
}
