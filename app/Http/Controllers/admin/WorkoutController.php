<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Models\Workout;
use Intervention\Image\ImageManager;

class WorkoutController extends Controller
{
    public function index(Request $request){
        $workouts = Workout::latest();
        if($request->get('keyword')){
            if (!empty($request->get('keyword'))) {
                $workouts = $workouts->where('name','like','%'.$request->get('keyword').'%');
            }
        }

        $workouts = $workouts->paginate(10);

        return view("admin.workout.list", compact('workouts'));
    }

    public function create() {
        $exercises = Exercise::orderBy('name')->get();
        // dd($exercises);
        return view("admin.workout.create",compact("exercises"));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            "name"=> "required",
            "slug"=> "required|unique:workouts",
            "description" =>"required|string|max:1000",
            "exercises"=> "required|array",
            'status' => 'required|in:0,1',
        ]);
        if ($validator->passes()) {

            $workout =  Workout::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
                'exercises' => $request->exercises,
                'duration' => count($request->exercises)
            ]);


            //Save Image Here
            if(!empty($request->image_id)){
                $tempImage = TempImage::find($request->image_id);

                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $workout->id.'.'.$ext;
                $sPath = public_path() .'/temp/'. $tempImage->name;
                $dPath = public_path() .'/uploads/workout/'. $newImageName;
                File::copy($sPath,$dPath);

                //Generate image thumbnail
                $sPathThumbnail = public_path() .'/temp/thumb/'. $tempImage->name;
                $dPathThumbnail = public_path() .'/uploads/workout/thumb/'. $newImageName;
                $img = ImageManager::gd()->read($sPathThumbnail);
                $img->resize(450, 600);
                $img->save($dPathThumbnail);

                $workout->image = $newImageName;
                $workout->save();

            }

            session()->flash("success","Workout added successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'Workout added successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function edit($workoutId, Request $request) {
        $exercises = Exercise::orderBy('name')->get();

        $workout = Workout::find($workoutId);
        if(empty($workout)){
            session()->flash('error','Workout not found');

            return redirect()->route('workouts.index');
        }


        return view('admin.workout.edit',compact('workout','exercises'));
    }

    public function update($workoutId, Request $request){
        $workout = Workout::find($workoutId);
        if(empty($workout)){
            session()->flash('error','Workout not found');

            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Workout not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            "name"=> "required",
            "slug" => "required|unique:meal_types,slug," . $workout->id . ",id",
            "description" =>"required|max:1000",
            "exercises"=>"required|array",
            'status' => 'required|in:0,1',
        ]);

        if ($validator->passes()) {
            $workout->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'description'=> $request->description,
                'exercises'=> $request->exercises,
                'duration' => count($request->exercises)

            ]);

            //Save Image Here
            if($request->image_id){
                $tempImage = TempImage::find($request->image_id);

                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $workout->id.'.'.$ext;
                $sPath = public_path() .'/temp/'. $tempImage->name;
                $dPath = public_path() .'/uploads/workout/'. $newImageName;
                File::copy($sPath,$dPath);

                //Generate image thumbnail
                $dPathThumbnail = public_path() .'/uploads/workout/thumb/'. $newImageName;
                $img = ImageManager::gd()->read($sPath);
                //$img->resize(450, 600);
                $img->resize(450, 600, function ($constraint) {
                    $constraint->upsize();
                });
                $img->save($dPathThumbnail);

                $workout->image = $newImageName;
                $workout->save();


            }

            session()->flash("success","Workout updated successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'Workout updated successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function destroy($workoutId, Request $request){
        $workout =  Workout::find($workoutId);
        if(empty($workout)){
            session()->flash('error','Workout not found');
            return response()->json([
                'status' => false,
                'message' => 'Workout not fpund'
            ]);
        }

        // Check if workout has an existing image
        if (!empty($workout->image)) {
            // Remove the previous image and thumbnail (if they exist)
            $oldImagePath = public_path('/uploads/workout/' . $workout->image);
            $oldThumbnailPath = public_path('/uploads/workout/thumb/' . $workout->image);

            if (file_exists($oldImagePath)) {
                unlink($oldImagePath); // Delete the old image
            }

            if (file_exists($oldThumbnailPath)) {
                unlink($oldThumbnailPath); // Delete the old thumbnail
            }
        }

        $workout->delete();

        session()->flash('success','Workout deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'Workout deleted successfully'
        ]);

    }
}
