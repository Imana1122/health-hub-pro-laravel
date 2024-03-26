<?php

namespace App\Listeners;

use App\Events\NotificationSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Pusher\Pusher;

class ProcessNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NotificationSent $event)
    {
        // Access the notification instance from the event
        $notification = $event->notification;
        // Initialize Pusher
        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            [
                'cluster' => env('PUSHER_APP_CLUSTER'), // Add this line
                'useTLS' => true, // Optional: Use TLS if available
            ]
        );

        // Trigger a Pusher event based on the notification
        try {
            $pusher->trigger("private-user.{$notification->user_id}", 'App\Events\NotificationSent', [
                'notification' => $notification->toArray(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error triggering Pusher event: ' . $e->getMessage());
        }
    }
}
