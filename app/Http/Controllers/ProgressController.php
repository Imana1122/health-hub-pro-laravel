<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;

class ProgressController extends Controller
{
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            "weight"=> "required|numeric",
            "height"=> "required|numeric",
            'front_image'=>'required|image',
            'back_image'=>'required|image',
            'right_image'=>'required|image',
            'left_image'=>'required|image',

        ]);

        if ($validator->passes()) {

            $progress = new Progress();
            $progress->weight = $request->weight;
            $progress->height = $request->height;
            $user = auth()->user();

            if($request->front_image){
                $image = $request->front_image;
                $ext = $image->getClientOriginalExtension();
                $newName = $user->id.'-'.now().'front'.'.'.$ext;

                $progress->front_image =$newName;


                $imagePath = $image->store('public/uploads/progress/front');

                Storage::move($imagePath, 'public/uploads/progress/front/' . $newName);



            }
            if($request->back_image){
                $image = $request->back_image;
                $ext = $image->getClientOriginalExtension();
                $newName = $user->id.'-'.now().'back'.'.'.$ext;

                $progress->back_image =$newName;

                $imagePath = $image->store('public/uploads/progress/back');




                Storage::move($imagePath, 'public/uploads/progress/back/' . $newName);


            }

            if($request->right_image){
                $image = $request->right_image;
                $ext = $image->getClientOriginalExtension();
                $newName = $user->id.'-'.now().'right'.'.'.$ext;

                $progress->right_image =$newName;

                $imagePath = $image->store('public/uploads/progress/right');




                Storage::move($imagePath, 'public/uploads/progress/right/' . $newName);


            }

            if($request->left_image){
                $image = $request->left_image;
                $ext = $image->getClientOriginalExtension();
                $newName = $user->id.'-'.now().'left'.'.'.$ext;

                $progress->left_image =$newName;
                $progress->save();

                $imagePath = $image->store('public/uploads/progress/left');


                Storage::move($imagePath, 'public/uploads/progress/left/' . $newName);


            }


            return response()->json([
                "status"=> true,
                "message"=> 'Progress added successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }
}
