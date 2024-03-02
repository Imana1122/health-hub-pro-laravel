<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Events\MessageSentToDietician;
use App\Models\ChatMessage;
use App\Models\Dietician;
use App\Models\DieticianBooking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatMessageController extends Controller
{
    public function getChatDieticians(){
        $userId = auth()->user()->id;

        $dieticianIdsWithRecentBookings = DieticianBooking::where('user_id', $userId)
        ->whereDate('created_at', '>', Carbon::now()->subDays(30))
        ->where('payment_status',1)
        ->pluck('dietician_id')
        ->toArray();

        $dieticians = Dietician::where('approved_status', 1)
            ->whereIn('id', $dieticianIdsWithRecentBookings)

            ->paginate(4);

        foreach ($dieticians as $dietician) {
            $messages = ChatMessage::where(function ($query) use ($dietician, $userId) {
                $query->where('sender_id', $userId)
                      ->where('receiver_id', $dietician->id);
            })
            ->orWhere(function ($query) use ($dietician, $userId) {
                $query->where('sender_id', $dietician->id)
                      ->where('receiver_id', $userId);
            })
            ->latest();


            $dietician->messages = $messages->get();
            // Attach the last message to the user model
            $dietician->last_message = $messages->first();
            $dietician->otherUserId = $userId;
        }


        return response()->json([
            'status'=>true,
            'data'=>$dieticians
        ]);

    }

    public function getChatUsers(){
        $dieticianId = auth()->user()->id;

        $userIdWithRecentBookings = DieticianBooking::where('dietician_id', $dieticianId)
        ->whereDate('created_at', '>', Carbon::now()->subDays(30))
        ->where('payment_status',1)
        ->pluck('user_id')
        ->toArray();


        $users = User::whereIn('id', $userIdWithRecentBookings)
        ->paginate(4);

        foreach ($users as $user) {
            $messages = ChatMessage::where(function ($query) use ($dieticianId, $user) {
                $query->where('sender_id', $dieticianId)
                      ->where('receiver_id', $user->id);
            })
            ->orWhere(function ($query) use ($user, $dieticianId) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $dieticianId);
            })
            ->latest();


            // Attach the last message to the user model
            $user->messages = $messages->get();
            $user->last_message = $messages->first();
            $user->otherUserId = $dieticianId;

        }


        return response()->json([
            'status'=>true,
            'data'=>$users
        ]);

    }

    public function loadChatMessages(){

    }




    public function storeByUser(Request $request){
        $validator = Validator::make($request->all(), [
            'dietician_id'=> 'required',
        ]);

        if ($validator->passes()){
            $otherUserId = $request->dietician_id;
            $userId = auth()->user()->id;
            $user = User::where('id',$userId)->first();
            $dietician = Dietician::where('id',$otherUserId)->first();

            $dieticianBooking = DieticianBooking::where('user_id',$userId)->where('dietician_id',$otherUserId)
            ->whereDate('created_at', '>', Carbon::now()->subDays(30))
            ->where('payment_status',1)->get();

            if ($dieticianBooking->isEmpty() || empty($user) || empty($dietician)){
                return response()->json([
                    'status'=>false,
                    'message'=>'You cannot send message to this dietician'
                ]);
            }else{
                if($request->message != '' || $request->message != null){
                    $chatMessage = new ChatMessage([
                        'message'=>$request->message
                    ]);
                // Assuming $user is the User model instance and $dietician is the Dietician model instance
                    $chatMessage->sender()->associate($user);
                    $chatMessage->receiver()->associate($dietician);
                    $chatMessage->save();

                    event(new MessageSentToDietician($chatMessage));

                    return response()->json([
                        'status'=>true,

                        'data'=>$chatMessage
                    ]);


                }else{

                }
            }
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function storeByDietician(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id'=> 'required',
        ]);

        if ($validator->passes()){
            $userId =auth()->user()->id;
            $otherUserId = $request->user_id;
            $user = User::where('id',$otherUserId)->first();
            // dd($userId,$otherUserId);
            $dietician = Dietician::where('id',$userId)->first();

            $dieticianBooking = DieticianBooking::where('user_id',$otherUserId)->where('dietician_id',$userId)
            ->whereDate('created_at', '>', Carbon::now()->subDays(30))
            ->where('payment_status',1)->get();

            if ($dieticianBooking->isEmpty() || empty($user) || empty($dietician)){
                return response()->json([
                    'status'=>false,
                    'message'=>'You cannot send message to this dietician'
                ]);
            }else{
                if($request->message != '' || $request->message != null){
                    $chatMessage = new ChatMessage([
                        'message'=>$request->message
                    ]);
                // Assuming $user is the User model instance and $dietician is the Dietician model instance
                    $chatMessage->sender()->associate($dietician);
                    $chatMessage->receiver()->associate($user);
                    $chatMessage->save();

                    event(new MessageSent($chatMessage));



                    return response()->json([
                        'status'=>true,
                        'data'=>$chatMessage
                    ]);


                }else{

                }
            }
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);

        }
    }
}
