<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use Intervention\Image\Facades\Image;

class CategoryController extends Controller
{
    public function index(Request $request){
        $categories = Category::latest();
        if($request->get('keyword')){
            if (!empty($request->get('keyword'))) {
                $categories = $categories->where('name','like','%'.$request->get('keyword').'%');
            }
        }

        $categories = $categories->paginate(10);

        return view("admin.category.list", compact('categories'));
    }

    public function create() {
        return view("admin.category.create");
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            "name"=> "required",
            "slug"=> "required|unique:categories",
            "status"=>"required"
        ]);

        if ($validator->passes()) {

            $category = Category::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'status' => $request->status,
            ]);

            //Save Image Here
            if(!empty($request->image_id)){
                $tempImage = TempImage::find($request->image_id);

                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'.'.$ext;
                $sPath = public_path() .'/temp/'. $tempImage->name;
                $dPath = public_path() .'/uploads/category/'. $newImageName;
                File::copy($sPath,$dPath);

                //Generate image thumbnail
                $dPathThumbnail = public_path() .'/uploads/category/thumb/'. $newImageName;
                $img = Image::make($sPath);
                //$img->resize(450, 600);
                $img->fit(450, 600, function ($constraint) {
                    $constraint->upsize();
                });
                $img->save($dPathThumbnail);

                $category->image = $newImageName;
                $category->save();

                // Delete the temporary image
                if (file_exists($sPath)) {
                    unlink($sPath);
                }

                // Delete the temporary image record from the database
                $tempImage->delete();

            }

            $request->session()->flash("success","Category added successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'Category added successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function edit($categoryId, Request $request) {

        $category = Category::find($categoryId);
        if(empty($category)){
            $request->session()->flash('error','Category not found');

            return redirect()->route('categories.index');
        }


        return view('admin.category.edit',compact('category'));
    }

    public function update($categoryId, Request $request){
        $category = Category::find($categoryId);
        if(empty($category)){
            $request->session()->flash('error','Category not found');

            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Category not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            "name"=> "required",
            "slug" => "required|unique:categories,slug," . $category->id . ",id",
            "status"=>"required"
        ]);

        if ($validator->passes()) {
            $category->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'status' => $request->status,
            ]);

            // Check if category has an existing image
            if (!empty($category->image)) {
                // Remove the previous image and thumbnail (if they exist)
                $oldImagePath = public_path('/uploads/category/' . $category->image);
                $oldThumbnailPath = public_path('/uploads/category/thumb/' . $category->image);

                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath); // Delete the old image
                }

                if (file_exists($oldThumbnailPath)) {
                    unlink($oldThumbnailPath); // Delete the old thumbnail
                }
            }

            //Save Image Here
            if(!empty($request->image_id)){
                $tempImage = TempImage::find($request->image_id);

                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'.'.$ext;
                $sPath = public_path() .'/temp/'. $tempImage->name;
                $dPath = public_path() .'/uploads/category/'. $newImageName;
                File::copy($sPath,$dPath);

                //Generate image thumbnail
                $dPathThumbnail = public_path() .'/uploads/category/thumb/'. $newImageName;
                $img = Image::make($sPath);
                //$img->resize(450, 600);
                $img->fit(450, 600, function ($constraint) {
                    $constraint->upsize();
                });
                $img->save($dPathThumbnail);

                $category->image = $newImageName;
                $category->save();

                // Delete the temporary image
                if (file_exists($sPath)) {
                    unlink($sPath);
                }

                // Delete the temporary image record from the database
                $tempImage->delete();
            }

            $request->session()->flash("success","Category updated successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'Category updated successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function destroy($categoryId, Request $request){
        $category =  Category::find($categoryId);
        if(empty($category)){
            $request->session()->flash('error','Category not found');
            return response()->json([
                'status' => false,
                'message' => 'Category not fpund'
            ]);
        }

        // Check if category has an existing image
        if (!empty($category->image)) {
            // Remove the previous image and thumbnail (if they exist)
            $oldImagePath = public_path('/uploads/category/' . $category->image);
            $oldThumbnailPath = public_path('/uploads/category/thumb/' . $category->image);

            if (file_exists($oldImagePath)) {
                unlink($oldImagePath); // Delete the old image
            }

            if (file_exists($oldThumbnailPath)) {
                unlink($oldThumbnailPath); // Delete the old thumbnail
            }
        }

        $category->delete();

        $request->session()->flash('success','Category deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully'
        ]);

    }
}
