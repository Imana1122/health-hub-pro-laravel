<?php

namespace App\Http\Controllers;

use App\Models\Allergen;
use App\Models\Cuisine;
use App\Models\HealthCondition;
use App\Models\User;
use App\Models\UserCuisine;
use App\Models\UserProfile;
use App\Models\WeightPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserProfileController extends Controller
{
    public function getWeightPlans(){
        $weightPlans = WeightPlan::all();

        return response()->json([
            'status' => true,
            'weightPlans' => $weightPlans
        ]);
    }

    public function getCuisines(){
        $cuisines = Cuisine::orderBy('name','ASC')->get();

        return response()->json([
            'status' => true,
            'cuisines' => $cuisines
        ]);
    }

    public function getAllergens(){
        $allergens = Allergen::orderBy('name','ASC')->get();

        return response()->json([
            'status' => true,
            'allergens' => $allergens
        ]);
    }

    public function getHealthConditions(){
        $healthConditions = HealthCondition::orderBy('name','ASC')->get();

        return response()->json([
            'status' => true,
            'healthConditions' => $healthConditions
        ]);
    }

    public function chooseGoal(Request $request){
        $userId = auth()->user()->id;

        if(!$userId){
            return response()->json([
                'error'=> 'User not found',
                'status'=>false
            ]);
        }

        $validator = Validator::make($request->all(), [
            'weight_plan_id' => "required",
        ]);

        if($validator->passes()){
            $userProfile = UserProfile::where('user_id',$userId)->first();

            $userProfile->update(
                [
                    'weight_plan_id' => $request->weight_plan_id,
                ]
            );


            return response()->json([
                'status' => true,
                'userProfile' => $userProfile
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }


    }


    public function setCuisines(Request $request){
        $userId = auth()->user()->id;
        $user = User::where('id', $userId)->first();

        if(!$userId){
            return response()->json([
                'error'=> 'User not found',
                'status'=>false
            ]);
        }

        $validator = Validator::make($request->all(), [
            'cuisines' => "nullable|array",
        ]);


        if($validator->passes()){
            $user->userCuisines()->delete();

            $userCuisines = $user->cuisines()->get();

            if($request->cuisines != null){
                $user->userCuisines()->createMany(
                    collect($request->cuisines)->map(function ($cuisine) use ($request) {
                        return ['cuisine_id' => $cuisine];
                    })->all()
                );
                $userCuisines = $user->cuisines()->get();

                return response()->json([
                    'status' => true,
                    'userCuisines' => $userCuisines,
                ]);
            }else{
                return response()->json([
                    'status' => true,
                    'userCuisines' => $userCuisines,
                ]);
            }



        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }


    }

    public function setAllergens(Request $request){
        $userId = auth()->user()->id;
        $user = User::where('id', $userId)->first();

        if(!$userId){
            return response()->json([
                'error'=> 'User not found',
                'status'=>false
            ]);
        }

        $validator = Validator::make($request->all(), [
            'allergens' => "nullable|array",
        ]);

        $user->userAllergens()->delete();

        if($validator->passes() ){
            $userAllergens = $user->allergens()->get();

            if($request->allergens != []){

            $user->userAllergens()->createMany(
                collect($request->allergens)->map(function ($allergen) use ($request) {
                    return ['allergen_id' => $allergen];
                })->all()
            );
            $userAllergens = $user->allergens()->get();


            return response()->json([
                'status' => true,
                'userAllergens' => $userAllergens
            ]);
        }
        else{
            return response()->json([
                'status' => true,
                'userAllergens' => $userAllergens
            ]);
        }

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }


    }

    public function setHealthConditions(Request $request){
        $userId = auth()->user()->id;
        $user = User::where('id', $userId)->first();

        if(!$userId){
            return response()->json([
                'error'=> 'User not found',
                'status'=>false
            ]);
        }

        $validator = Validator::make($request->all(), [
            'healthConditions' => "nullable|array",
        ]);
        $user->userHealthConditions()->delete();

        if($validator->passes()){
        $userHealthConditions = $user->healthConditions()->get();

        if($request->healthConditions != null){
            $user->userHealthConditions()->createMany(
                collect($request->healthConditions)->map(function ($healthConditon) {
                    return ['health_condition_id' => $healthConditon];
                })->all()
            );
            $userHealthConditions = $user->healthConditions()->get();

            return response()->json([
                'status' => true,
                'userHealthConditions' => $userHealthConditions
            ]);
        }else{
            return response()->json([
                'status' => true,
                'userHealthConditions' => $userHealthConditions
            ]);
        }

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }


    }


}
