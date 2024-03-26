<?php

namespace App\Http\Controllers;


use App\Models\Notification;

class NotificationController extends Controller
{


    public function getNotifications(){
        $id = auth()->user()->id;

        $notifications = Notification::where('user_id',$id)->where('user_type','App\Models\User')->where('scheduled_at','<',now())->orWhere('scheduled_at',null)->paginate(10);

        return response()->json([
            'status'=>true,
            'data'=>$notifications
        ]);


    }

    public function readNotifications(){
        $user = auth()->user()->id;

        Notification::where('user_id',$user)->where('scheduled_at','<',now())->update([
            'read'=>1
        ]);

        return response()->json([
            'status'=>true,
            'message'=>'Notifications read'
        ]);
    }
}
