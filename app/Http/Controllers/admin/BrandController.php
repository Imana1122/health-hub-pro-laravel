<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index(Request $request){
        $brands = Brand::latest();
        if($request->get('keyword')){
            if (!empty($request->get('keyword'))) {
                $brands = $brands->where('name','like','%'.$request->get('keyword').'%');
            }
        }

        $brands = $brands->paginate(10);

        return view("admin.brands.list", compact('brands'));
    }

    public function create(){
        return view("admin.brands.create");
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            "name"=> "required",
            "slug"=> "required|unique:brands",
            "status"=>"required"
        ]);

        if ($validator->passes()) {

            Brand::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'status' => $request->status,
            ]);


            $request->session()->flash("success","Brand added successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'Brand added successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function edit($brandId, Request $request){
        $brand = Brand::find($brandId);
        if(empty($brand)){
            $request->session()->flash('error','Brand not found');

            return redirect()->route('brands.index');
        }


        return view('admin.brands.edit',compact('brand'));
    }

    public function update($brandId, Request $request){
        $brand = Brand::find($brandId);
        if(empty($brand)){
            $request->session()->flash('error','Brand not found');

            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Brand not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            "name"=> "required",
            "slug" => "required|unique:brands,slug," . $brand->id . ",id",
            "status"=>"required"
        ]);

        if ($validator->passes()) {
            $brand->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'status' => $request->status,
            ]);

            $request->session()->flash("success","Brand updated successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'Brand updated successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function destroy($brandId, Request $request){
        $brand =  Brand::find($brandId);
        if(empty($brand)){
            $request->session()->flash('error','Brand not found');
            return response()->json([
                'status' => false,
                'message' => 'Brand not fpund'
            ]);
        }

        $brand->delete();

        $request->session()->flash('success','Brand deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'Brand deleted successfully'
        ]);
    }
}
