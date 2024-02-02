<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Allergen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AllergenController extends Controller
{

    public function index(Request $request){
        $allergens = Allergen::latest();

        if ($request->get('keyword')) {
            if (!empty($request->get('keyword'))) {
                $allergens = $allergens->where('name', 'like', '%' . $request->get('keyword') . '%');
            }
        }

        $allergens = $allergens->paginate(10);

        return view("admin.allergens.list", compact('allergens'));
    }

    public function create(){

        return view('admin.allergens.create');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'=> 'required',
            'slug' => 'required|unique:allergens',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->passes()){
            $allergen = Allergen::create(
                $request->only('name', 'slug', 'status'
            ));

            session()->flash('success','Allergen created successfully');

            return response()->json([
                'status' => true,
                'message'=> 'Allergen created successfully.'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function edit($allergenId, Request $request) {
        $allergen = Allergen::find($allergenId);
        if(empty($allergen)){
            session()->flash('error','Sub Category not found');

            return redirect()->route('allergens.index');
        }


        return view('admin.allergens.edit',compact('allergen'));
    }

    public function update($allergenId, Request $request){
        $allergen = Allergen::find($allergenId);
        if(empty($allergen)){
            session()->flash('error','Allergen not found');

            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Sub Category not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            "name"=> "required",
            "slug" => "required|unique:allergens,slug," . $allergen->id . ",id",
            'status' => 'required|in:0,1',
        ]);

        if ($validator->passes()) {
            $allergen->update(
                $request->only('name', 'slug', 'status'
            ));



            session()->flash("success","Allergen updated successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'Allergen updated successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function destroy($allergenId, Request $request){
        $allergen =  Allergen::find($allergenId);
        if(empty($allergen)){
            session()->flash('error','Allergen not found');
            return response()->json([
                'status' => false,
                'message' => 'Allergen not fpund'
            ]);
        }

        $allergen->delete();

        session()->flash('success','Allergen deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'Allergen deleted successfully'
        ]);

    }
}
