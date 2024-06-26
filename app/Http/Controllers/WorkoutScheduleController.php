<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use App\Models\CustomizedWorkout;
use App\Models\Exercise;
use App\Models\Notification;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WorkoutScheduleController extends Controller
{
    public function scheduleWorkout(Request $request){
        $validator = Validator::make($request->all(), [
            "workout_id" => "required",
            'scheduled_time' => "required",

        ]);

        if ($validator->passes()) {
            $workoutSchedule = new WorkoutSchedule([
                'user_id'=> auth()->user()->id,
                'scheduled_time' => $request->scheduled_time,
                'notifiable' =>1,

            ]);
            if($request->get('type')=='customized'){
                $workout = CustomizedWorkout::where('id',$request->workout_id)->first();

            }else{
                $workout = Workout::where('id',$request->workout_id)->first();

            }
            $workoutSchedule->workout()->associate($workout);
            $workoutSchedule->save();

            $notification = new Notification([
                'image' => asset('storage/uploads/workout/' . $workout->image),
                'message' => $workout->name . " is scheduled for " . $request->scheduled_time,
            ]);
            $user=User::where('id',auth()->user()->id)->first();

            // Assuming $user is the User model instance and $dietician is the Dietician model instance
            $notification->user()->associate($user);
            $notification->save();
            $notification = Notification::where('id',$notification->id)->first();
            $notification->to = 'user';
            event(new NotificationSent($notification));

            return response()->json(['status'=>true,'message'=>'Workout is successfully scheduled.']);

        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }

    public function getScheduledWorkouts($now){
        $date = date('Y-m-d', strtotime($now));
        $scheduledWorkouts = WorkoutSchedule::with('workout')->where('user_id',auth()->user()->id)->whereDate('scheduled_time',$date)->get();



        return response()->json([
            'status'=>true,
            'data'=>$scheduledWorkouts
        ]);
    }



    public function getUpcomingWorkouts(){
        $upcomingWorkouts = WorkoutSchedule::with('workout')->where('user_id',auth()->user()->id)->whereDate('scheduled_time','>' ,now())->where('done',0)->limit(3)->get();

        return response()->json([
            'status'=>true,
            'data'=>$upcomingWorkouts
        ]);
    }

    public function updateNotifiable(Request $request){
        $validator = Validator::make($request->all(), [
            "notifiable" => "required",
            'id' => "required",

        ]);

        if ($validator->passes()) {
            $upcomingWorkout = WorkoutSchedule::where('id', $request->id)->first();

            if ($upcomingWorkout) {

                $upcomingWorkout->notifiable = $request->notifiable;
                $upcomingWorkout->save();

                return response()->json([
                    'status'=>true,
                    'message'=>'Workout notifiable updated successfully!'
                ]);
            }
            return response()->json([
                'status'=>false,
                'message'=>'Workout scheduled not found!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }


    }

    public function updateDone(Request $request){
        $validator = Validator::make($request->all(), [
            "done" => "required",
            'id' => "required",

        ]);

        if ($validator->passes()) {
            $upcomingWorkout = WorkoutSchedule::where('id', $request->id)->first();

            if ($upcomingWorkout) {

                $upcomingWorkout->done = $request->done;
                $upcomingWorkout->save();

                return response()->json([
                    'status'=>true,
                    'message'=>'Workout done successfully!'
                ]);
            }
            return response()->json([
                'status'=>false,
                'message'=>'Workout scheduled not found!'
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }


    }
}
