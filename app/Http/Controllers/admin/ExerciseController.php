<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Models\Exercise;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ExerciseController extends Controller
{
    public function index(Request $request){
        $exercises = Exercise::latest();
        if($request->get('keyword')){
            if (!empty($request->get('keyword'))) {
                $exercises = $exercises->where('name','like','%'.$request->get('keyword').'%');
            }
        }

        $exercises = $exercises->paginate(10);

        return view("admin.exercise.list", compact('exercises'));
    }

    public function create() {
        return view("admin.exercise.create");
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            "name"=> "required",
            "slug"=> "required|unique:meal_types",
            "description" =>"required|string|max:1000",
            "metabolic_equivalent"=> "required",
            'status' => 'required|in:0,1',
            'image_id' =>'required'
        ]);

        if ($validator->passes()) {

            $exercise = Exercise::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
                'metabolic_equivalent'=> $request->metabolic_equivalent,

            ]);

            //Save Image Here
            if(!empty($request->image_id)){
                $tempImage = TempImage::find($request->image_id);

                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $exercise->id.'.'.$ext;
                $sPath = public_path() .'/temp/'. $tempImage->name;
                $dPath = 'uploads/exercise/' . $newImageName;
                File::copy($sPath, public_path('/storage/' . $dPath)); // Copy file to storage

                // Generate image thumbnail
                $sPathThumbnail = public_path() .'/temp/thumb/'. $tempImage->name;
                $dPathThumbnail = 'uploads/exercise/thumb/' . $newImageName;
                $img = ImageManager::gd()->read($sPathThumbnail);
                $img->resize(450, 600);
                $img->save(public_path('/storage/' . $dPathThumbnail)); // Save thumbnail to storage

                // Move the files within the storage disk
                Storage::move($dPath, $dPath); // Move original image
                Storage::move($dPathThumbnail, $dPathThumbnail); // Move thumbnail



                $exercise->image = $newImageName;
                $exercise->save();




            }

            session()->flash("success","Exercise added successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'Exercise added successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function edit($exerciseId, Request $request) {

        $exercise = Exercise::find($exerciseId);
        if(empty($exercise)){
            session()->flash('error','Exercise not found');

            return redirect()->route('exercises.index');
        }


        return view('admin.exercise.edit',compact('exercise'));
    }

    public function update($exerciseId, Request $request){
        $exercise = Exercise::find($exerciseId);
        if(empty($exercise)){
            session()->flash('error','Exercise not found');

            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Exercise not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            "name"=> "required",
            "slug" => "required|unique:meal_types,slug," . $exercise->id . ",id",
            "description" =>"required|max:1000",
            "metabolic_equivalent"=> "required",
            'status' => 'required|in:0,1',
        ]);

        if ($validator->passes()) {
            $exercise->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'description'=> $request->description,
                'metabolic_equivalent'=> $request->metabolic_equivalent,

            ]);

            //Save Image Here
            if($request->image_id){
                $tempImage = TempImage::find($request->image_id);

                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $exercise->id.'.'.$ext;
                $sPath = public_path() .'/temp/'. $tempImage->name;
                $dPath = public_path() .'/storage/uploads/exercise/'. $newImageName;
                File::copy($sPath,$dPath);

                //Generate image thumbnail
                $dPathThumbnail = public_path() .'/storage/uploads/exercise/thumb/'. $newImageName;
                $img = ImageManager::gd()->read($sPath);
                //$img->resize(450, 600);
                $img->resize(450, 600, function ($constraint) {
                    $constraint->upsize();
                });
                $img->save($dPathThumbnail);



                $exercise->image = $newImageName;
                $exercise->save();


            }

            session()->flash("success","Exercise updated successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'Exercise updated successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function destroy($exerciseId, Request $request){
        $exercise =  Exercise::find($exerciseId);
        if(empty($exercise)){
            session()->flash('error','Exercise not found');
            return response()->json([
                'status' => false,
                'message' => 'Exercise not fpund'
            ]);
        }

        // Check if exercise has an existing image
        if (!empty($exercise->image)) {
            // Remove the previous image and thumbnail (if they exist)
            $oldImagePath = public_path('/storage/uploads/exercise/' . $exercise->image);
            $oldThumbnailPath = public_path('/storage/uploads/exercise/thumb/' . $exercise->image);

            if (file_exists($oldImagePath)) {
                unlink($oldImagePath); // Delete the old image
            }

            if (file_exists($oldThumbnailPath)) {
                unlink($oldThumbnailPath); // Delete the old thumbnail
            }
        }

        $exercise->delete();

        session()->flash('success','Exercise deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'Exercise deleted successfully'
        ]);

    }
}
