<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Cuisine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class CuisineController extends Controller
{
    public function index(Request $request){
        $cuisines = Cuisine::latest();
        if($request->get('keyword')){
            if (!empty($request->get('keyword'))) {
                $cuisines = $cuisines->where('name','like','%'.$request->get('keyword').'%');
            }
        }

        $cuisines = $cuisines->paginate(10);

        return view("admin.cuisines.list", compact('cuisines'));
    }

    public function create(){
        return view("admin.cuisines.create");
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            "name"=> "required",
            "slug"=> "required|unique:cuisines",
            'status' => 'required|in:0,1',
        ]);

        if ($validator->passes()) {

            Cuisine::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'status' => $request->status,
            ]);


            session()->flash("success","Cuisine added successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'Cuisine added successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function edit($cuisineId, Request $request){
        $cuisine = Cuisine::find($cuisineId);
        if(empty($cuisine)){
            session()->flash('error','Cuisine not found');

            return redirect()->route('cuisines.index');
        }


        return view('admin.cuisines.edit',compact('cuisine'));
    }

    public function update($cuisineId, Request $request){
        $cuisine = Cuisine::find($cuisineId);
        if(empty($cuisine)){
            session()->flash('error','Cuisine not found');

            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Cuisine not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            "name"=> "required",
            "slug" => "required|unique:cuisines,slug," . $cuisine->id . ",id",
            'status' => 'required|in:0,1',
        ]);

        if ($validator->passes()) {
            $cuisine->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'status' => $request->status,
            ]);

            session()->flash("success","Cuisine updated successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'Cuisine updated successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function destroy($cuisineId, Request $request){
        $cuisine =  Cuisine::find($cuisineId);
        if(empty($cuisine)){
            session()->flash('error','Cuisine not found');
            return response()->json([
                'status' => false,
                'message' => 'Cuisine not fpund'
            ]);
        }

        $cuisine->delete();

        session()->flash('success','Cuisine deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'Cuisine deleted successfully'
        ]);
    }
}
