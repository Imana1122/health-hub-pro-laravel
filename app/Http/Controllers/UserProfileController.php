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
            'data' => $weightPlans
        ]);
    }

    public function getCuisines(){
        $cuisines = Cuisine::orderBy('name','ASC')->get();

        return response()->json([
            'status' => true,
            'data' => $cuisines
        ]);
    }

    public function getAllergens(){
        $allergens = Allergen::orderBy('name','ASC')->get();

        return response()->json([
            'status' => true,
            'data' => $allergens
        ]);
    }

    public function getHealthConditions(){
        $healthConditions = HealthCondition::orderBy('name','ASC')->get();

        return response()->json([
            'status' => true,
            'data' => $healthConditions
        ]);
    }

    public function chooseGoal(Request $request){
        $userId = auth()->user()->id;

        if(!$userId){
            return response()->json([
                'message'=> 'User not found',
                'status'=>false
            ]);
        }

        $validator = Validator::make($request->all(), [
            'weight_plan_id' => "required",
        ]);


        if($validator->passes()){
            $userProfile = UserProfile::where('user_id',$userId)->first();
            $weightPlan=WeightPlan::where('id',$request->weight_plan_id)->first();

            if($userProfile == null && $weightPlan == null){
                return response()->json([
                    'status' => false,
                    'error' => 'No weight plan or userprofile with this id!'
                ]);
            }



            $userGoals = $this->getTodayGoal();

            // dd($userGoals);
            $userProfile->update(
                [
                    'weight_plan_id' => $request->weight_plan_id,
                    'calories' => $userGoals['calories'],
                    'protein' => $userGoals['protein'],
                    'carbohydrates' => $userGoals['carbohydrates'],
                    'total_fat' => $userGoals['total_fat'],
                    'sodium' => $userGoals['sodium'],
                    'sugar' => $userGoals['sugar'],
                    'bmi'=>$userGoals['bmi'],

                ]
            );
            $userProfile=UserProfile::where('user_id',auth()->user()->id)->first();
            $userProfile->weight_plan =  $weightPlan->title;

            return response()->json([
                'status' => true,
                'data' => $userProfile
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }


    }

    public function getTodayGoal(){

        // Assuming $userProfile contains the user's profile data

        $userProfile = UserProfile::where('user_id',auth()->user()->id)->first();
        $userGoal = WeightPlan::where('id',$userProfile->weight_plan_id)->first();

        // Step 2: Calculate BMR
        $bmr = $this->calculateBMR($userProfile);
        $bmi = $this->calculateBMI($userProfile);

        // Step 3: Adjust BMR for Activity Level to get TDEE
        $tdee = $this->calculateTDEE($bmr, $userProfile->activity_level);

        // Step 4: Set Calorie Goals based on user's goal (e.g., weight loss, muscle gain)
        $calorieGoal = $this->calculateCalorieGoal($tdee, $userGoal,$userProfile->calorie_difference);

        // Step 5: Calculate Macronutrient Requirements based on predefined ratios
        $macronutrientGoals = $this->calculateMacronutrientGoals($userProfile, $userGoal);

        // Step 6: Adjust for Individual Needs (optional)

        // Step 7: Calculate Sodium and Sugar Intake limits
        $sodiumLimit = $this->calculateSodiumLimit();
        if($userProfile->gender == 'female'){
            $sugarLimit = $this->calculateSugarLimitForWomen();

        }else{
            $sugarLimit = $this->calculateSugarLimitForMen();

        }

        // Step 8: Store calculated goals in user's profile or a separate table
        $userMealPlan =
            [
                'calories' => $calorieGoal,
                'protein' => $macronutrientGoals['protein'],
                'carbohydrates' => $macronutrientGoals['carbohydrate'],
                'total_fat' => $macronutrientGoals['fat'],
                'sodium' => $sodiumLimit,
                'sugar' => $sugarLimit,
                'bmi'=>$bmi
            ];


        // Return the list of meal plans
        return $userMealPlan;
    }


    function calculateSugarLimitForMen() {
        // Define sugar limit based on dietary guidelines for men
        return 36; // Example: Daily sugar limit in grams for men
    }

    function calculateSugarLimitForWomen() {
        // Define sugar limit based on dietary guidelines for women
        return 25; // Example: Daily sugar limit in grams for women
    }

        // Step 2: Calculate BMR
    function calculateBMR($userProfile) {
        $bmr = 0;
        if ($userProfile->gender == 'male') {
            $bmr = 10 * $userProfile->weight + 6.25 * $userProfile->height - 5 * $userProfile->age + 5;
        } elseif ($userProfile->gender == 'female') {

            $bmr = 10 * $userProfile->weight + 6.25 * $userProfile->height - 5 * $userProfile->age - 161;
        }
        return $bmr;
    }
    function calculateBMI($userProfile) {
        $heightInMeters = $userProfile->height / 100;
        return $userProfile->weight / ($heightInMeters * $heightInMeters);
    }


    // Step 3: Adjust BMR for Activity Level to get TDEE
    function calculateTDEE($bmr, $activityLevel) {
        $activityFactors = [
            'sedentary' => 1.2,
            'lightly_active' => 1.375,
            'moderately_active' => 1.55,
            'very_active' => 1.725,
            'extra_active' => 1.9,
        ];
        return $bmr * $activityFactors[$activityLevel];
    }

    // Step 4: Set Calorie Goals based on user's goal
    function calculateCalorieGoal($tdee, $userGoal,$calorieDifference) {
        switch ($userGoal) {
            case 'muscle-gain':
                return $tdee + $calorieDifference;
            case 'weight-loss':
                return $tdee - $calorieDifference;
            case 'fat-loss':
                return $tdee - $calorieDifference;
            case 'maintain-weight':
            default:
                return $tdee; // Maintain weight
        }
    }

    function calculateMacronutrientGoals($profile, $userGoal) {
        // Calculate macronutrient goals
        $proteinGoal = $profile->weight * $userGoal->protein_ratio;
        $carbGoal =  $profile->weight  *  $userGoal->carb_ratio;
        $fatGoal =  $profile->weight  *  $userGoal->fat_ratio;

        return [
            'protein' => $proteinGoal,
            'carbohydrate' => $carbGoal,
            'fat' => $fatGoal,
        ];
    }


    // Step 7: Calculate Sodium and Sugar Intake limits
    function calculateSodiumLimit() {
        // Define sodium limit based on dietary guidelines
        return 2300; // Example: Daily sodium limit in milligrams
    }


    public function setCuisines(Request $request){
        $userId = auth()->user()->id;
        $user = User::where('id', $userId)->first();

        if(!$userId){
            return response()->json([
                'message'=> 'User not found',
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
                    'data' => $userCuisines,
                ]);
            }else{
                return response()->json([
                    'status' => true,
                    'data' => $userCuisines,
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
                'message'=> 'User not found',
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
                'data' => $userAllergens
            ]);
        }
        else{
            return response()->json([
                'status' => true,
                'data' => $userAllergens
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
                'message'=> 'User not found',
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
                'data' => $userHealthConditions
            ]);
        }else{
            return response()->json([
                'status' => true,
                'data' => $userHealthConditions
            ]);
        }

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }


    }

    public function changeNotification(){
        $userProfile = UserProfile::where('user_id',auth()->user()->id)->first();
        if($userProfile == null){
            return response()->json([
                'status'=>false,
                'message'=>'User profile not found'
            ]);
        }else{
            if($userProfile->notification ==1){
                $userProfile->notification=0;
                $userProfile->save();
                return response()->json([
                    'status'=>true,
                    'message'=>'Pop up notification blocked.',
                    'data'=>0
                ]);
            }else{
                $userProfile->notification=1;
                $userProfile->save();
                return response()->json([
                    'status'=>true,
                    'message'=>'Pop up notification activated.',
                    'data'=>1
                ]);
            }

        }
    }
}
