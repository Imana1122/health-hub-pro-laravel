<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use App\Models\CustomizedWorkout;
use App\Models\Notification;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkoutLogController extends Controller
{
    public function logWorkout(Request $request){
        $validator = Validator::make($request->all(), [
            "workout_id" => "required",
            'start_at' => "required",
            'end_at' => 'required',
            'workout_name' => 'required',
            'completion_status' => 'required',
            'calories_burned' => 'required',

        ]);

        if ($validator->passes()) {
            $workoutLog = new WorkoutLog([
                'user_id'=> auth()->user()->id,
                'start_at' => $request->start_at,
                'end_at' => $request->end_at,
                'workout_name' => $request->workout_name,
                'calories_burned' => $request->calories_burned,
                'completion_status' => $request->completion_status,

            ]);
            if($request->get('type')=='customized'){
                $workout = CustomizedWorkout::where('id',$request->workout_id)->first();

            }else{
                $workout = Workout::where('id',$request->workout_id)->first();

            }
            if($workout){
                $workoutLog->workout()->associate($workout);
                $workoutLog->save();

                $notification = new Notification([
                    'image' => asset('storage/uploads/workout/' . $workout->image),
                    'message' => $workout->name . " is logged.",
                ]);

                $user=User::where('id',auth()->user()->id)->first();

                // Assuming $user is the User model instance and $dietician is the Dietician model instance
                $notification->user()->associate($user);
                $notification->save();
                $notification = Notification::where('id',$notification->id)->first();
                $notification->to = 'user';

                event(new NotificationSent($notification));

                // Extract the date part from the provided datetime string
                $providedDate = date('Y-m-d', strtotime($request->end_at));

                // Retrieve UserWorkoutLog records with workout data, including images and ingredients
                $workoutLogs = WorkoutLog::where('user_id', auth()->user()->id)
                    ->whereRaw('DATE(end_at) = ?', [$providedDate])
                    ->get();
                return response()->json([
                    'status' => true,
                    'data' => $workoutLogs
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'errors' => "Workout not found"
                ]);
            }

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function getWorkoutLogs($now){


        // Extract the date part from the provided datetime string
        $providedDate = date('Y-m-d', strtotime($now));

        // Retrieve UserWorkoutLog records with workout data, including images and ingredients
        $workoutLogs = WorkoutLog::where('user_id', auth()->user()->id)
            ->whereRaw('DATE(created_at) = ?', [$providedDate])
            ->get();


        return response()->json([
            'status' => true,
            'data' => $workoutLogs
        ]);


    }

    public function deleteWorkoutLog($id,Request $request){
        $workoutLog = WorkoutLog::where('id',$id)->first();
        if($workoutLog){
            $workoutLog->delete();

            // Extract the date part from the provided datetime string
            $providedDate = date('Y-m-d', strtotime($request->created_at));

            // Retrieve UserWorkoutLog records with workout data, including images and ingredients
            $workoutLogs = WorkoutLog::where('user_id', auth()->user()->id)
                ->whereRaw('DATE(created_at) = ?', [$providedDate])
                ->get();

            return response()->json([
                'status' => true,
                'data' => $workoutLogs
            ]);
        }else{
            return response()->json([
                'status' => false,
                'message' => "Workout log not found"
            ]);
        }

    }

    public function getWorkoutLineGraphDetails($type)
    {
        // Step 1: Query UserWorkoutLog to retrieve the logs of workouts logged by the user
        $userWorkoutLogs = WorkoutLog::with('workout')->where('user_id', auth()->user()->id)->get();
        if ($userWorkoutLogs->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'No user logs found'
            ]);
        }
        // Step 3: Group the data based on the $type parameter
        $lineChartData = [];
        switch ($type) {
            case 'daily':
                $lineChartData = $this->getDataForDaily($userWorkoutLogs);
                break;
            case 'weekly':
                $lineChartData = $this->getDataForWeekly($userWorkoutLogs);
                break;
            case 'monthly':
                $lineChartData = $this->getDataForMonthly($userWorkoutLogs);
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

    private function getDataForDaily($workoutLogs)
    {
        // Implement logic to process data for daily basis
        $dailyData = [];
        foreach ($workoutLogs as $log) {
            $date = $log->created_at->format('Y-m-d');
            $calories_burned = $log->calories_burned;
            if (!isset($dailyData[$date])) {
                $dailyData[$date] = 0;
            }
            $dailyData[$date] += $calories_burned;
        }
        $formattedData = [];
        foreach ($dailyData as $date => $calories_burned) {
            $formattedData[] = ['x' => $date, 'y' =>$calories_burned ];
        }
        return $formattedData;
    }


    private function getDataForWeekly($logDetails)
    {
        // Implement logic to process data for weekly basis
        // For example, group data by week and calculate total calories_burned for each week
        $weeklyData = [];
        foreach ($logDetails as $log) {
            $week = $log->created_at->format('W');
            $calories_burned = $log->calories_burned;
            if (!isset($weeklyData[$week])) {
                $weeklyData[$week] = 0;
            }
            $weeklyData[$week] += $calories_burned;
        }
        $formattedData = [];
        foreach ($weeklyData as $week => $calories_burned) {
            // Assuming the year is not considered for weekly data
            $formattedData[] = ['x' => 'Week ' . $week, 'y' => $calories_burned];
        }
        return $formattedData;
    }

    private function getDataForMonthly($logDetails)
    {
        // Implement logic to process data for monthly basis
        // For example, group data by month and calculate total calories_burned for each month
        $monthlyData = [];
        foreach ($logDetails as $log) {
            $month = $log->created_at->format('Y-m');
            $calories_burned = $log->calories_burned;
            if (!isset($monthlyData[$month])) {
                $monthlyData[$month] = 0;
            }
            $monthlyData[$month] += $calories_burned;
        }
        $formattedData = [];
        foreach ($monthlyData as $month => $calories_burned) {
            $formattedData[] = ['x' => $month, 'y' => $calories_burned];
        }
        return $formattedData;
    }
}
