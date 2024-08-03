<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        // trip start reminder
        //first reminder
        $schedule->command('trips:send-reminders')->dailyAt('08:00');
        //second reminder
        $schedule->command('trips:send-reminders')->dailyAt('12:00');
        //third reminder
        $schedule->command('trips:send-reminders')->dailyAt('18:00');
        $schedule->command('overdue:reminders')->dailyAt('23:45');
        //document reminder
        $schedule->command('documents:send-expiration-reminders')->dailyAt('08:00');
        $schedule->command('documents:send-expiration-reminders')->dailyAt('18:00');

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
