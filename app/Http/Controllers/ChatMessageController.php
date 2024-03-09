<?php

namespace App\Http\Controllers;

use App\Events\MessageRead;
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
            $messagesQuery = ChatMessage::where(function ($query) use ($dietician, $userId) {
                $query->where('sender_id', $userId)
                      ->where('receiver_id', $dietician->id);
            })
            ->orWhere(function ($query) use ($dietician, $userId) {
                $query->where('sender_id', $dietician->id)
                      ->where('receiver_id', $userId);
            });

            // Get the total number of messages
            $totalMessages = $messagesQuery->count();

            // Define the number of messages per page
            $perPage = 10;

            // Calculate the total number of pages
            $totalPages = ceil($totalMessages / $perPage);

            // Determine the page number to retrieve (default to last page)
            $pageNumber = max(1, min($totalPages, request()->query('page', $totalPages)));

            // Retrieve messages for the requested page
            $messages = $messagesQuery->paginate($perPage, ['*'], 'page', $pageNumber);
            $dietician->messages=$messages;
            // Attach the last message to the user model
            $dietician->last_message = $messagesQuery->first();
            $dietician->otherUserId = $userId;
        }


        return response()->json([
            'status'=>true,
            'data'=>$dieticians
        ]);

    }

    public function loadMoreMessages($id){
        $userId = auth()->user()->id;

        $messagesQuery = ChatMessage::where(function ($query) use ($id, $userId) {
            $query->where('sender_id', $userId)
                    ->where('receiver_id', $id);
        })
        ->orWhere(function ($query) use ($id, $userId) {
            $query->where('sender_id', $id)
                    ->where('receiver_id', $userId);
        });

        // Get the total number of messages
        $totalMessages = $messagesQuery->count();

        // Define the number of messages per page
        $perPage = 10;

        // Calculate the total number of pages
        $totalPages = ceil($totalMessages / $perPage);

        // Determine the page number to retrieve (default to last page)
        $pageNumber = max(1, min($totalPages, request()->query('page', $totalPages)));

        // Retrieve messages for the requested page
        $messages = $messagesQuery->orderBy('created_at')->paginate($perPage, ['*'], 'page', $pageNumber);

        return response()->json([
            'status'=>true,
            'data'=>$messages
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
            $messagesQuery = ChatMessage::where(function ($query) use ($dieticianId, $user) {
                $query->where('sender_id', $dieticianId)
                      ->where('receiver_id', $user->id);
            })
            ->orWhere(function ($query) use ($user, $dieticianId) {
                $query->where('sender_id', $user->id)
                      ->where('receiver_id', $dieticianId);
            });
            // Get the total number of messages
            $totalMessages = $messagesQuery->count();

            // Define the number of messages per page
            $perPage = 10;

            // Calculate the total number of pages
            $totalPages = ceil($totalMessages / $perPage);

            // Determine the page number to retrieve (default to last page)
            $pageNumber = max(1, min($totalPages, request()->query('page', $totalPages)));

            // Retrieve messages for the requested page
            $messages = $messagesQuery->paginate($perPage, ['*'], 'page', $pageNumber);
            $user->messages=$messages;
            $user->last_message = $messagesQuery->latest()->first();
            $user->otherUserId = $dieticianId;

        }


        return response()->json([
            'status'=>true,
            'data'=>$users
        ]);

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
                    $chatMessage = ChatMessage::where('id',$chatMessage->id)->first();

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
                    $chatMessage = ChatMessage::where('id',$chatMessage->id)->first();

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

    public function setChatMessagesRead(Request $request){
        $validator = Validator::make($request->all(), [
            'sender_id'=> 'required',
        ]);
        $receiverId = auth()->user()->id;

        if ($validator->passes()){
           $senderId=$request->sender_id;
        // Perform the update operation
        $count = ChatMessage::where('sender_id', $senderId)
            ->where('receiver_id', $receiverId)
            ->where('read', 0)
            ->update(['read' => 1]);

        // Check if the updated chat messages count is greater than 1
        // if ($count > 1) {
            // Trigger the event
            event(new MessageRead($receiverId, $senderId));
        // }



            return response()->json([
                'status' => true,
            ]);
         }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);

        }


    }
}
