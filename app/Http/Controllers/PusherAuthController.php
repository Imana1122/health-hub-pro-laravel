<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Pusher\Pusher;

class PusherAuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $user = auth()->user();

        if ($user) {
            $pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                ['cluster' => env('PUSHER_APP_CLUSTER')]
            );


            $channelName = $request->channel_name;
            $socketId = $request->socket_id;
            $customData = json_encode(['user_id' => $user->id]); // Correct JSON formatting
            $authSignature = $pusher->authorizeChannel($channelName, $socketId, $customData);

            return response()->json(['auth' => $authSignature]);
        } else {
            return response()->json(['message' => 'Unauthorized']);
        }
    }
}
