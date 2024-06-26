<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use App\Models\MealPlan;
use App\Models\Notification;
use App\Models\Recipe;
use App\Models\User;
use App\Models\UserMealPlan;
use App\Models\UserProfile;
use App\Models\UserRecipeLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserMealPlanController extends Controller
{

    public function index(){
        $userId = auth()->user()->id;
        $userProfile = UserProfile::where('user_id',$userId)->first();


        // Define the target values
        $targetValues = [
            'calories' => $userProfile->calories,
            'protein' => $userProfile->protein,
            'total_fat' => $userProfile->total_fat,
            'carbohydrates' => $userProfile->carbohydrates,
            'sodium' => $userProfile->sodium,
            'sugar' => $userProfile->sugar,
        ];

        // Query to retrieve meal plans
        $mealPlansQuery = MealPlan::with('breakfastRecipe.images','breakfastRecipe.meal_type')
            ->with('snackRecipe.images','snackRecipe.meal_type')
            ->with('lunchRecipe.images','lunchRecipe.meal_type')
            ->with('dinnerRecipe.images','dinnerRecipe.meal_type')
            ->select('*');

        // Calculate the overall difference
        $mealPlansQuery->orderByRaw("
            ABS(calories - {$targetValues['calories']}) +
            ABS(protein - {$targetValues['protein']}) +
            ABS(total_fat - {$targetValues['total_fat']}) +
            ABS(carbohydrates - {$targetValues['carbohydrates']}) +
            ABS(sodium - {$targetValues['sodium']}) +
            ABS(sugar - {$targetValues['sugar']})
        ");

        // Define the pagination limit
        $perPage = 1;

        // Paginate the results
        $closestMealPlans = $mealPlansQuery->paginate($perPage);

        return response()->json(['status'=>true,'data'=>$closestMealPlans]);
    }

    public function selectMealPlan(Request $request){
        $validator = Validator::make($request->all(), [
            "meal_plan_id" => "required",
            'created_at' => "required"
        ]);

        if ($validator->passes()) {
            $userMealPlan = UserMealPlan::where('user_id',auth()->user()->id)->whereDate('created_at',now())->first();
            $userProfile = UserProfile::where('user_id',auth()->user()->id)->first();
            if (empty($userMealPlan)) {
                $userMealPlan = UserMealPlan::create([
                    'user_id' => auth()->user()->id,
                    'meal_plan_id'=>$request->meal_plan_id,
                    'calories' => $userProfile->calories,
                    'carbohydrates' => $userProfile->carbohydrates,
                    'protein' => $userProfile->protein,
                    'total_fat' => $userProfile->total_fat,
                    'sodium' => $userProfile->sodium,
                    'sugar' => $userProfile->sugar,
                ]);


                $notification = new Notification([
                    'message' =>  "New meal plan is selected.",
                ]);

                $user=User::where('id',auth()->user()->id)->first();

                // Assuming $user is the User model instance and $dietician is the Dietician model instance
                $notification->user()->associate($user);
                $notification->save();
                $notification = Notification::where('id',$notification->id)->first();
                $notification->to = 'user';
                event(new NotificationSent($notification));



            } else {
                $userMealPlan->update([
                    'calories' => $userProfile->calories,
                    'carbohydrates' => $userProfile->carbohydrates,
                    'meal_plan_id'=>$request->meal_plan_id,
                    'protein' => $userProfile->protein,
                    'total_fat' => $userProfile->total_fat,
                    'saturated_fat' => $userProfile->saturated_fat,
                    'sodium' => $userProfile->sodium,
                    'sugar' => $userProfile->sugar,
                ]);



                $notification = new Notification([
                    'message' =>  "Meal plan is updated.",
                ]);

                $user=User::where('id',auth()->user()->id)->first();

                // Assuming $user is the User model instance and $dietician is the Dietician model instance
                $notification->user()->associate($user);
                $notification->save();
                $notification = Notification::where('id',$notification->id)->first();
                $notification->to = 'user';
                event(new NotificationSent($notification));

            }


            return response()->json([
                'status' => true,
                'message' => 'Meal Plan selected successfully!'
            ]);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }



}
