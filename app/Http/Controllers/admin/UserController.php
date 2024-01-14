<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request){
        $users = User::latest('created_at');

        if(!empty($request->get('keyword'))){
            $users = $users->where('name','like','%'.$request->get('keyword'). '%')
                    ->orwhere('phone_number','like','%'.$request->get('keyword'). '%');

        }

        $users = $users->paginate(10);

        return view('admin.users.list', compact('users'));

    }

    public function create(Request $request){

        return view('admin.users.create');

    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'=> 'required|min:3',
            'phone_number'=> 'required|unique:users,phone_number',
            'password' => 'required|confirmed|min:5'
        ]);

        if($validator->passes()){

            $user = new User();
            $user->name = $request->name;
            $user->phone_number = $request->phone_number;
            $user->password = $request->password;
            $user->save();

            $message = 'User added successfully';
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
        $user = User::find($id);
        if($user == null){
            $message = 'User not found';
            session()->flash('error', $message);
            return redirect()->route('users.index');
        }

        return view('admin.users.edit', compact('user'));

    }

    public function update(Request $request, $id){
        $user = User::find($id);

        if($user == null){
            $message = 'User not found';
            session()->flash('error', $message);
            return redirect()->route('users.index');
        }

        $validator = Validator::make($request->all(), [
            'name'=> 'required|min:3',
            'phone_number'=> 'required|unique:users,phone_number,'. $id . ',id',
            'password' => 'nullable|confirmed|min:5'
        ]);

        if($validator->passes()){

            $user->name = $request->name;
            $user->phone_number = $request->phone_number;
            $user->status = $request->status;
            $user->save();

            $message = 'User updated successfully';
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
        $user = User::find($id);

        if($user == null){
            $message = 'User not found';
            session()->flash('error', $message);
            return redirect()->route('users.index');
        }
        $message = 'User deleted successfully';
        $user->delete();
        session()->flash('success', $message);
        return response()->json([
            'status'=>true,
            'message'=> $message
        ]);
    }
}
