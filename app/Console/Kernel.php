<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('notifications:send-scheduled')->everyDay();
        // Within the handle method
        try {
            $schedule->command('app:notify-breakfast-time')->dailyAt('10:00');
            $schedule->command('app:notify-lunch-time')->dailyAt('13:00');
            $schedule->command('app:notify-snack-time')->dailyAt('15:00');
            $schedule->command('app:notify-dinner-time')->dailyAt('17:00');


        } catch (\Exception $e) {
            Log::error('Error in command: ' . $e->getMessage());
        }

    }

    /**
     * Register the commands for the application.
     */

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
