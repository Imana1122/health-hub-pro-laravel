<?php

namespace App\Http\Controllers;


use App\Models\Notification;

class NotificationController extends Controller
{


    public function getNotifications(){
        $id = auth()->user()->id;

        $notifications = Notification::where('user_id', $id)
        ->where(function ($query) {
            $query->where('scheduled_at', '<', now())
                ->orWhereNull('scheduled_at');
        })->latest()
        ->paginate(5);

        return response()->json([
            'status'=>true,
            'data'=>$notifications
        ]);

    }



    public function readNotifications(){
        $user = auth()->user()->id;

        Notification::where('user_id',$user)->where('scheduled_at','<',now())->orWhere('scheduled_at',null)->update([
            'read'=>1
        ]);

        return response()->json([
            'status'=>true,
        ]);
    }
}
