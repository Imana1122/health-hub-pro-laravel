<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{

    public function index(Request $request){
        $subCategories = SubCategory::select('sub_categories.*', 'categories.name as categoryName')
            ->latest('id')
            ->leftJoin('categories', 'categories.id', '=', 'sub_categories.category_id');

        if ($request->get('keyword')) {
            if (!empty($request->get('keyword'))) {
                $subCategories = $subCategories->where('sub_categories.name', 'like', '%' . $request->get('keyword') . '%');
            }
        }

        $subCategories = $subCategories->paginate(10);

        return view("admin.sub_category.list", compact('subCategories'));
    }

    public function create(){
        $categories = Category::orderBy('name','ASC')->get();
        return view('admin.sub_category.create', compact('categories'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'=> 'required',
            'slug' => 'required|unique:sub_categories',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required',
        ]);

        if ($validator->passes()){
            $subCategory = SubCategory::create(
                $request->only('name', 'slug', 'category_id', 'status'
            ));

            $request->session()->flash('success','Sub category created successfully');

            return response()->json([
                'status' => true,
                'message'=> 'Sub category created successfully.'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function edit($subCategoryId, Request $request) {
        $categories = Category::orderBy('name','ASC')->get();
        $subCategory = SubCategory::find($subCategoryId);
        if(empty($subCategory)){
            $request->session()->flash('error','Sub Category not found');

            return redirect()->route('sub-categories.index');
        }


        return view('admin.sub_category.edit',compact('subCategory','categories'));
    }

    public function update($subCategoryId, Request $request){
        $subCategory = SubCategory::find($subCategoryId);
        if(empty($subCategory)){
            $request->session()->flash('error','SubCategory not found');

            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Sub Category not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            "name"=> "required",
            "slug" => "required|unique:sub_categories,slug," . $subCategory->id . ",id",
            "status"=>"required",
            'category_id' => 'required|exists:categories,id'
        ]);

        if ($validator->passes()) {
            $subCategory->update(
                $request->only('name', 'slug', 'category_id', 'status'
            ));



            $request->session()->flash("success","Sub category updated successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'Sub category updated successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function destroy($subCategoryId, Request $request){
        $subCategory =  SubCategory::find($subCategoryId);
        if(empty($subCategory)){
            $request->session()->flash('error','SubCategory not found');
            return response()->json([
                'status' => false,
                'message' => 'SubCategory not fpund'
            ]);
        }

        $subCategory->delete();

        $request->session()->flash('success','SubCategory deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'SubCategory deleted successfully'
        ]);

    }
}
