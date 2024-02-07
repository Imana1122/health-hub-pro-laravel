<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Models\MealType;
use Intervention\Image\ImageManager;

class MealTypeController extends Controller
{
    public function index(Request $request){
        $mealTypes = MealType::latest();
        if($request->get('keyword')){
            if (!empty($request->get('keyword'))) {
                $mealTypes = $mealTypes->where('name','like','%'.$request->get('keyword').'%');
            }
        }

        $mealTypes = $mealTypes->paginate(10);

        return view("admin.mealType.list", compact('mealTypes'));
    }

    public function create() {
        return view("admin.mealType.create");
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            "name"=> "required",
            "slug"=> "required|unique:meal_types",
            'status' => 'required|in:0,1',
        ]);

        if ($validator->passes()) {

            $mealType = MealType::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'status' => $request->status,
            ]);

            //Save Image Here
            if(!empty($request->image_id)){
                $tempImage = TempImage::find($request->image_id);

                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $mealType->id.'.'.$ext;
                $sPath = public_path() .'/temp/'. $tempImage->name;
                $dPath = public_path() .'/uploads/mealType/'. $newImageName;
                File::copy($sPath,$dPath);

                //Generate image thumbnail
                $sPathThumbnail = public_path() .'/temp/thumb/'. $tempImage->name;
                $dPathThumbnail = public_path() .'/uploads/mealType/thumb/'. $newImageName;
                $img = ImageManager::gd()->read($sPathThumbnail);
                $img->resize(450, 600);
                $img->save($dPathThumbnail);

                $mealType->image = $newImageName;
                $mealType->save();
            }

            session()->flash("success","MealType added successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'MealType added successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function edit($mealTypeId, Request $request) {

        $mealType = MealType::find($mealTypeId);
        if(empty($mealType)){
            session()->flash('error','MealType not found');

            return redirect()->route('mealTypes.index');
        }


        return view('admin.mealType.edit',compact('mealType'));
    }

    public function update($mealTypeId, Request $request){
        $mealType = MealType::find($mealTypeId);
        if(empty($mealType)){
            session()->flash('error','MealType not found');

            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'MealType not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            "name"=> "required",
            "slug" => "required|unique:meal_types,slug," . $mealType->id . ",id",
            'status' => 'required|in:0,1',
        ]);

        if ($validator->passes()) {
            $mealType->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'status' => $request->status,
            ]);

            //Save Image Here
            if($request->image_id){
                $tempImage = TempImage::find($request->image_id);

                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $mealType->id.'.'.$ext;
                $sPath = public_path() .'/temp/'. $tempImage->name;
                $dPath = public_path() .'/uploads/mealType/'. $newImageName;
                File::copy($sPath,$dPath);

                //Generate image thumbnail
                $dPathThumbnail = public_path() .'/uploads/mealType/thumb/'. $newImageName;
                $img = ImageManager::gd()->read($sPath);
                //$img->resize(450, 600);
                $img->resize(450, 600, function ($constraint) {
                    $constraint->upsize();
                });
                $img->save($dPathThumbnail);

                $mealType->image = $newImageName;
                $mealType->save();


            }

            session()->flash("success","MealType updated successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'MealType updated successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function destroy($mealTypeId, Request $request){
        $mealType =  MealType::find($mealTypeId);
        if(empty($mealType)){
            session()->flash('error','MealType not found');
            return response()->json([
                'status' => false,
                'message' => 'MealType not fpund'
            ]);
        }

        // Check if mealType has an existing image
        if (!empty($mealType->image)) {
            // Remove the previous image and thumbnail (if they exist)
            $oldImagePath = public_path('/uploads/mealType/' . $mealType->image);
            $oldThumbnailPath = public_path('/uploads/mealType/thumb/' . $mealType->image);

            if (file_exists($oldImagePath)) {
                unlink($oldImagePath); // Delete the old image
            }

            if (file_exists($oldThumbnailPath)) {
                unlink($oldThumbnailPath); // Delete the old thumbnail
            }
        }

        $mealType->delete();

        session()->flash('success','MealType deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'MealType deleted successfully'
        ]);

    }
}
