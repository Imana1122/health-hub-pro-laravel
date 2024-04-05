<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use App\Models\Notification;
use App\Models\Recipe;
use App\Models\User;
use App\Models\UserMealPlan;
use App\Models\UserProfile;
use App\Models\UserRecipeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserRecipeLogController extends Controller
{
    public function logMeal(Request $request){
        $validator = Validator::make($request->all(), [
            "recipe_id" => "required",
            'created_at' => "required"
        ]);

        if ($validator->passes()) {
            $userMealPlan = UserMealPlan::where('user_id',auth()->user()->id)->whereDate('created_at',$request->created_at)->first();
            $userProfile = UserProfile::where('user_id',auth()->user()->id)->first();
            if (empty($userMealPlan)) {
                $userMealPlan = UserMealPlan::create([
                    'user_id' => auth()->user()->id,
                    'calories' => $userProfile->calories,

                    'carbohydrates' => $userProfile->carbohydrates,
                    'protein' => $userProfile->protein,
                    'total_fat' => $userProfile->total_fat,
                    'saturated_fat' => $userProfile->saturated_fat,
                    'sodium' => $userProfile->sodium,
                    'sugar' => $userProfile->sugar,
                ]);
            } else {
                $userMealPlan->update([
                    'calories' => $userProfile->calories,

                    'carbohydrates' => $userProfile->carbohydrates,
                    'protein' => $userProfile->protein,
                    'total_fat' => $userProfile->total_fat,
                    'saturated_fat' => $userProfile->saturated_fat,
                    'sodium' => $userProfile->sodium,
                    'sugar' => $userProfile->sugar,
                ]);
            }

            $recipeLog = UserRecipeLog::create([
                'user_id'=> auth()->user()->id,
                'recipe_id' => $request->recipe_id,
                'created_at' => $request->created_at,
                'updated_at' => $request->created_at,

            ]);


            $recipe= Recipe::where('id',$request->recipe_id)->first();
            $notification = new Notification([
                'image' => asset('storage/uploads/recipes/small/' . $recipe->images[0]->image),
                'message' => $recipe->title . " is logged.",
            ]);

            $user=User::where('id',auth()->user()->id)->first();

            // Assuming $user is the User model instance and $dietician is the Dietician model instance
            $notification->user()->associate($user);
            $notification->save();
            $notification = Notification::where('id',$notification->id)->first();
            $notification->to = 'user';
            event(new NotificationSent($notification));

            if($recipeLog){

                // Extract the date part from the provided datetime string
                $providedDate = date('Y-m-d', strtotime($request->created_at));

                // Retrieve UserRecipeLog records with recipe data, including images and ingredients
                $recipeLogs = UserRecipeLog::with(['recipe.images'])
                    ->where('user_id', auth()->user()->id)
                    ->whereRaw('DATE(created_at) = ?', [$providedDate])
                    ->get();
                return response()->json([
                    'status' => true,
                    'data' => [
                        'recipeLogs'=>$recipeLogs,
                        'userNutrients'=>$userMealPlan
                    ]
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'errors' => 'Recipe log not found'
                ]);
            }

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }




    }

    public function getMealLogs($now){
        $userMealPlan = UserMealPlan::where('user_id',auth()->user()->id)->whereDate('created_at',$now)->first();


        // Extract the date part from the provided datetime string
        $providedDate = date('Y-m-d', strtotime($now));

        // Retrieve UserRecipeLog records with recipe data, including images and ingredients
        $recipeLogs = UserRecipeLog::with(['recipe.images', 'recipe.ingredient'])
            ->where('user_id', auth()->user()->id)
            ->whereRaw('DATE(created_at) = ?', [$providedDate])
            ->get();

            return response()->json([
                'status' => true,
                'data' => [
                    'recipeLogs'=>$recipeLogs,
                    'userNutrients'=>$userMealPlan
                ]
            ]);


    }

    public function deleteMealLog($id,Request $request){
        $recipeLog = UserRecipeLog::where('id',$id)->first();
        if($recipeLog){
            $recipeLog->delete();

            // Extract the date part from the provided datetime string
            $providedDate = date('Y-m-d', strtotime($request->created_at));

            // Retrieve UserRecipeLog records with recipe data, including images and ingredients
            $recipeLogs = UserRecipeLog::with(['recipe.images', 'recipe.ingredient'])
                ->where('user_id', auth()->user()->id)
                ->whereRaw('DATE(created_at) = ?', [$providedDate])
                ->get();

            return response()->json([
                'status' => true,
                'data' => $recipeLogs
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => "Recipe log not found"
            ]);
        }

    }

    public function getLineGraphDetails(Request $request,$type)
    {
        $year=$request->get('year');
        $month=$request->get('month');

        // Step 1: Query UserRecipeLog to retrieve the logs of recipes logged by the user
        $userRecipeLogs = UserRecipeLog::with('recipe')->where('user_id', auth()->user()->id)->orderBy('created_at');
        if($type == 'monthly'){
            $userRecipeLogs=$userRecipeLogs->whereYear('created_at',$year)->get();
        }else{
            $userRecipeLogs=$userRecipeLogs->whereYear('created_at',$year)->whereMonth('created_at',$month)->get();

        }

        if ($userRecipeLogs->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'No user logs found'
            ]);
        }
        // Step 3: Group the data based on the $type parameter
        $lineChartData = [];
        switch ($type) {
            case 'daily':
                $lineChartData = $this->getDataForDaily($userRecipeLogs);
                break;
            case 'weekly':
                $lineChartData = $this->getDataForWeekly($userRecipeLogs);
                break;
            case 'monthly':
                $lineChartData = $this->getDataForMonthly($userRecipeLogs);
                break;
            default:
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid type. Please provide "daily", "weekly", or "monthly".'
                ]);
        }

        // Return the line chart data to your frontend
        return response()->json([
            'status' => true,
            'data' => $lineChartData
        ]);
    }

    private function getDataForDaily($recipeLogs)
    {
        // Implement logic to process data for daily basis
        $dailyData = [];
        foreach ($recipeLogs as $log) {
            $date = $log->created_at->format('d');
            $calories = $log->recipe->calories;
            if (!isset($dailyData[$date])) {
                $dailyData[$date] = 0;
            }
            $dailyData[$date] += $calories;
        }
        $formattedData = [];
        foreach ($dailyData as $date => $calories) {
            $formattedData[] = ['x' => $date, 'y' => $calories];
        }
        return $formattedData;
    }


    private function getDataForWeekly($logDetails)
    {
        // Implement logic to process data for weekly basis
        // For example, group data by week and calculate total calories for each week
        $weeklyData = [];
        foreach ($logDetails as $log) {
            $week = $log->created_at->format('W');
            $calories = $log->recipe->calories;
            if (!isset($weeklyData[$week])) {
                $weeklyData[$week] = 0;
            }
            $weeklyData[$week] += $calories;
        }
        $formattedData = [];
        foreach ($weeklyData as $week => $calories) {
            // Assuming the year is not considered for weekly data
            $formattedData[] = ['x' => $week, 'y' => $calories];
        }
        return $formattedData;
    }

    private function getDataForMonthly($logDetails)
    {
        // Implement logic to process data for monthly basis
        // For example, group data by month and calculate total calories for each month
        $monthlyData = [];
        foreach ($logDetails as $log) {
            $month = $log->created_at->format('m');
            $calories = $log->recipe->calories;
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = 0;
            }
            $monthlyData[$month] += $calories;
        }
        $formattedData = [];
        foreach ($monthlyData as $month => $calories) {
            $formattedData[] = ['x' => $month, 'y' => $calories];
        }
        return $formattedData;

    }
}
