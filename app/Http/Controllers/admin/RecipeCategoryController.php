<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Models\RecipeCategory;
use Intervention\Image\ImageManager;

class RecipeCategoryController extends Controller
{
    public function index(Request $request){
        $recipeCategories = RecipeCategory::latest();
        if($request->get('keyword')){
            if (!empty($request->get('keyword'))) {
                $recipeCategories = $recipeCategories->where('name','like','%'.$request->get('keyword').'%');
            }
        }

        $recipeCategories = $recipeCategories->paginate(10);

        return view("admin.recipeCategory.list", compact('recipeCategories'));
    }

    public function create() {
        return view("admin.recipeCategory.create");
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            "name"=> "required",
            "slug"=> "required|unique:recipe_categories",
            'status' => 'required|in:0,1',
        ]);

        if ($validator->passes()) {

            $recipeCategory = RecipeCategory::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'status' => $request->status,
            ]);

            //Save Image Here
            if(!empty($request->image_id)){
                $tempImage = TempImage::find($request->image_id);

                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $recipeCategory->id.'.'.$ext;
                $sPath = public_path() .'/temp/'. $tempImage->name;
                $dPath = public_path() .'/storage/uploads/recipeCategory/'. $newImageName;
                File::copy($sPath,$dPath);

                //Generate image thumbnail
                $sPathThumbnail = public_path() .'/temp/thumb/'. $tempImage->name;
                $dPathThumbnail = public_path() .'/storage/uploads/recipeCategory/thumb/'. $newImageName;
                $img = ImageManager::gd()->read($sPathThumbnail);
                $img->resize(450, 600);
                $img->save($dPathThumbnail);

                $recipeCategory->image = $newImageName;
                $recipeCategory->save();




            }

            session()->flash("success","RecipeCategory added successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'RecipeCategory added successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function edit($recipeCategoryId, Request $request) {

        $recipeCategory = RecipeCategory::find($recipeCategoryId);
        if(empty($recipeCategory)){
            session()->flash('error','RecipeCategory not found');

            return redirect()->route('recipeCategories.index');
        }


        return view('admin.recipeCategory.edit',compact('recipeCategory'));
    }

    public function update($recipeCategoryId, Request $request){
        $recipeCategory = RecipeCategory::find($recipeCategoryId);
        if(empty($recipeCategory)){
            session()->flash('error','RecipeCategory not found');

            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'RecipeCategory not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            "name"=> "required",
            "slug" => "required|unique:recipe_categories,slug," . $recipeCategory->id . ",id",
            'status' => 'required|in:0,1',
        ]);

        if ($validator->passes()) {
            $recipeCategory->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'status' => $request->status,
            ]);

            //Save Image Here
            if($request->image_id){
                $tempImage = TempImage::find($request->image_id);

                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $recipeCategory->id.'.'.$ext;
                $sPath = public_path() .'/temp/'. $tempImage->name;
                $dPath = public_path() .'/storage/uploads/recipeCategory/'. $newImageName;
                File::copy($sPath,$dPath);

                //Generate image thumbnail
                $dPathThumbnail = public_path() .'/storage/uploads/recipeCategory/thumb/'. $newImageName;
                $img = ImageManager::gd()->read($sPath);
                //$img->resize(450, 600);
                $img->resize(450, 600, function ($constraint) {
                    $constraint->upsize();
                });
                $img->save($dPathThumbnail);

                $recipeCategory->image = $newImageName;
                $recipeCategory->save();


            }

            session()->flash("success","RecipeCategory updated successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'RecipeCategory updated successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function destroy($recipeCategoryId, Request $request){
        $recipeCategory =  RecipeCategory::find($recipeCategoryId);
        if(empty($recipeCategory)){
            session()->flash('error','RecipeCategory not found');
            return response()->json([
                'status' => false,
                'message' => 'RecipeCategory not fpund'
            ]);
        }

        // Check if recipeCategory has an existing image
        if (!empty($recipeCategory->image)) {
            // Remove the previous image and thumbnail (if they exist)
            $oldImagePath = public_path('/storage/uploads/recipeCategory/' . $recipeCategory->image);
            $oldThumbnailPath = public_path('/storage/uploads/recipeCategory/thumb/' . $recipeCategory->image);

            if (file_exists($oldImagePath)) {
                unlink($oldImagePath); // Delete the old image
            }

            if (file_exists($oldThumbnailPath)) {
                unlink($oldThumbnailPath); // Delete the old thumbnail
            }
        }

        $recipeCategory->delete();

        session()->flash('success','RecipeCategory deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'RecipeCategory deleted successfully'
        ]);

    }
}
