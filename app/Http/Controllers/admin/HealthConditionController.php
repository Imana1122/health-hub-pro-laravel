<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\HealthCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HealthConditionController extends Controller
{

    public function index(Request $request){
        $healthConditions = HealthCondition::latest();

        if ($request->get('keyword')) {
            if (!empty($request->get('keyword'))) {
                $healthConditions = $healthConditions->where('name', 'like', '%' . $request->get('keyword') . '%');
            }
        }

        $healthConditions = $healthConditions->paginate(10);

        return view("admin.healthConditions.list", compact('healthConditions'));
    }

    public function create(){

        return view('admin.healthConditions.create');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'name'=> 'required',
            'slug' => 'required|unique:health_conditions',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->passes()){
            $healthCondition = HealthCondition::create(
                $request->only('name', 'slug', 'status'
            ));

            session()->flash('success','Health Condition created successfully');

            return response()->json([
                'status' => true,
                'message'=> 'Health Condition created successfully.'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function edit($healthConditionId, Request $request) {
        $healthCondition = HealthCondition::find($healthConditionId);
        if(empty($healthCondition)){
            session()->flash('error','Sub Category not found');

            return redirect()->route('healthConditions.index');
        }


        return view('admin.healthConditions.edit',compact('healthCondition'));
    }

    public function update($healthConditionId, Request $request){
        $healthCondition = HealthCondition::find($healthConditionId);
        if(empty($healthCondition)){
            session()->flash('error','HealthCondition not found');

            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Sub Category not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            "name"=> "required",
            "slug" => "required|unique:health_conditions,slug," . $healthCondition->id . ",id",
            'status' => 'required|in:0,1',
        ]);

        if ($validator->passes()) {
            $healthCondition->update(
                $request->only('name', 'slug', 'status'
            ));



            session()->flash("success","Health Condition updated successfully");

            return response()->json([
                "status"=> true,
                "message"=> 'Health Condition updated successfully'
            ]);

        }else{
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    public function destroy($healthConditionId, Request $request){
        $healthCondition =  HealthCondition::find($healthConditionId);
        if(empty($healthCondition)){
            session()->flash('error','HealthCondition not found');
            return response()->json([
                'status' => false,
                'message' => 'HealthCondition not fpund'
            ]);
        }

        $healthCondition->delete();

        session()->flash('success','HealthCondition deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'HealthCondition deleted successfully'
        ]);

    }
}
