<?php

namespace App\Console\Commands;

use App\Events\NotificationSent;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Pusher\Pusher;

class SendScheduledNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-scheduled';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled notifications';

    /**
     * Execute the console command.
     */


    public function handle()
    {
      

        $notifications = Notification::whereDate('scheduled_at',now())->get();
        // Push notifications to Pusher
        // Dispatch NotificationSent event for each notification
        foreach ($notifications as $notification) {
            // Dispatch the event
            $notification=Notification::where('id',$notification->id)->first();
            $notification->to='user';

            event(new NotificationSent($notification));

        }

        $this->info('Scheduled notifications sent successfully.');
    }

}
