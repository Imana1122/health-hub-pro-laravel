<?php

namespace App\Http\Controllers;

use App\Models\CustomizedWorkout;
use App\Models\Exercise;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;

class CustomizedWorkoutController extends Controller
{
    public function getCustomizedWorkouts(Request $request){
        $workouts = CustomizedWorkout::orderBy('name','ASC');
        if ($request->get('keyword') != '') {
            $workouts = $workouts->where('workouts.name', 'like', '%' . $request->input('keyword') . '%')->orWhere('dieticians.scheduled_time', 'like', '%' . $request->input('keyword') . '%');
        }
        $workouts= $workouts->paginate(3);

        // Fetch all exercises
        $exercises = Exercise::all()->keyBy('id');

        // Manipulate the data to replace exercise IDs with exercise objects

        $workouts->each(function ($workout) use ($exercises) {
            $workout->exercises = collect($workout->exercises)->map(function ($exerciseId) use ($exercises) {
                return $exercises->get(intval($exerciseId));
            });
        });
        return response()->json([
            'status' => true,
            'data' => $workouts
        ]);
    }
    public function getExercises(){
        $exercises=Exercise::orderBy('name')->get();
        return response()->json([
            'status'=>true,
            'data'=>$exercises
        ]);
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            "name"=> "required",
            "slug"=> "required|unique:customized_workouts",
            "description" =>"required|string|max:1000",
            "exercises"=> "required",
            'image'=>'required','count'=>'required'
        ]);

        if ($validator->passes()) {
            $exercises = json_decode($request->exercises);
            $array = $exercises;

            // Initialize an empty associative array
            $assocArray = [];

            // Iterate through the array and assign each element to the corresponding index starting from 1
            foreach ($array as $index => $value) {
                $assocArray[$index + 1] = $value;
            }

            $workout =  CustomizedWorkout::create([
                'user_id'=>auth()->user()->id,
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
                'exercises' => $assocArray,
                'duration' => $request->count
            ]);


            if($request->image){
                $image = $request->image;
                $ext = $image->getClientOriginalExtension();
                $newName = $workout->id.'.'.$ext;

                $workout->image =$newName;
                $workout->save();

                $image->move(public_path().'/uploads/workout/',$newName);

                //Generate thumbnail
                $sourcePath = public_path().'/uploads/workout/'.$newName; // Fix the path
                $destPath = public_path().'/uploads/workout/thumb/'.$newName; // Fix the path

                $image = ImageManager::gd()->read($sourcePath);
                $image->resize(450, 600);
                $image->save($destPath);

            }


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
}
