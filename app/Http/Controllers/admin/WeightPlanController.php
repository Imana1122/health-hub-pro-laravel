<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use App\Models\WeightPlan;
use Intervention\Image\ImageManager;

class WeightPlanController extends Controller
{
    public function index(Request $request){
        $weightPlans = WeightPlan::latest();
        if($request->get('keyword')){
            if (!empty($request->get('keyword'))) {
                $weightPlans = $weightPlans->where('title','like','%'.$request->get('keyword').'%');
            }
        }

        $weightPlans = $weightPlans->paginate(10);

        return view("admin.weightPlan.list", compact('weightPlans'));
    }

    public function create() {
        return view("admin.weightPlan.create");
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            "title"=> "required",
            "slug"=> "required|unique:weight_plans",
            'status' => 'required|in:0,1',
            'subtitle' => 'required'

        ]);

        if ($validator->passes()) {

            $weightPlan = WeightPlan::create([
                'title' => $request->title,
                'slug' => $request->slug,
                'status' => $request->status,
                'subtitle' => $request->subtitle

            ]);

            //Save Image Here
            if(!empty($request->image_id)){
                $tempImage = TempImage::find($request->image_id);

                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $weightPlan->id.'.'.$ext;
                $sPath = public_path() .'/temp/'. $tempImage->name;
                $dPath = public_path() .'/uploads/weightPlan/'. $newImageName;
                File::copy($sPath,$dPath);

                //Generate image thumbnail
                $sPathThumbnail = public_path() .'/temp/thumb/'. $tempImage->name;
                $dPathThumbnail = public_path() .'/uploads/weightPlan/thumb/'. $newImageName;
                $img = ImageManager::gd()->read($sPathThumbnail);
                $img->resize(450, 600);
                $img->save($dPathThumbnail);

                $weightPlan->image = $newImageName;
                $weightPlan->save();




            }

            session()->flash("success","WeightPlan added successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'WeightPlan added successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function edit($weightPlanId, Request $request) {

        $weightPlan = WeightPlan::find($weightPlanId);
        if(empty($weightPlan)){
            session()->flash('error','WeightPlan not found');

            return redirect()->route('weightPlans.index');
        }


        return view('admin.weightPlan.edit',compact('weightPlan'));
    }

    public function update($weightPlanId, Request $request){
        $weightPlan = WeightPlan::find($weightPlanId);
        if(empty($weightPlan)){
            session()->flash('error','WeightPlan not found');

            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'WeightPlan not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            "title"=> "required",
            "slug" => "required|unique:weight_plans,slug," . $weightPlan->id . ",id",
            'status' => 'required|in:0,1',
            'subtitle' => 'required'
        ]);

        if ($validator->passes()) {
            $weightPlan->update([
                'title' => $request->title,
                'slug' => $request->slug,
                'status' => $request->status,
                'subtitle' => $request->subtitle
            ]);

            //Save Image Here
            if($request->image_id){
                $tempImage = TempImage::find($request->image_id);

                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $weightPlan->id.'.'.$ext;
                $sPath = public_path() .'/temp/'. $tempImage->name;
                $dPath = public_path() .'/uploads/weightPlan/'. $newImageName;
                File::copy($sPath,$dPath);

                //Generate image thumbnail
                $dPathThumbnail = public_path() .'/uploads/weightPlan/thumb/'. $newImageName;
                $img = ImageManager::gd()->read($sPath);
                $img->resize(450, 600);

                $img->save($dPathThumbnail);

                $weightPlan->image = $newImageName;
                $weightPlan->save();


            }

            session()->flash("success","WeightPlan updated successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'WeightPlan updated successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function destroy($weightPlanId, Request $request){
        $weightPlan =  WeightPlan::find($weightPlanId);
        if(empty($weightPlan)){
            session()->flash('error','WeightPlan not found');
            return response()->json([
                'status' => false,
                'message' => 'WeightPlan not fpund'
            ]);
        }

        // Check if weightPlan has an existing image
        if (!empty($weightPlan->image)) {
            // Remove the previous image and thumbnail (if they exist)
            $oldImagePath = public_path('/uploads/weightPlan/' . $weightPlan->image);
            $oldThumbnailPath = public_path('/uploads/weightPlan/thumb/' . $weightPlan->image);

            if (file_exists($oldImagePath)) {
                unlink($oldImagePath); // Delete the old image
            }

            if (file_exists($oldThumbnailPath)) {
                unlink($oldThumbnailPath); // Delete the old thumbnail
            }
        }

        $weightPlan->delete();

        session()->flash('success','WeightPlan deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'WeightPlan deleted successfully'
        ]);

    }
}
