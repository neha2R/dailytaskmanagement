<?php

namespace App\Console\Commands;

use App\Models\Trip;
use App\Notifications\AndroidNotification;
use Illuminate\Console\Command;

class SendTripStartNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trips:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send trip reminders to drivers for trips starting today';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = now()->startOfDay();

        $trips = Trip::whereDate('start_date', $today)->where('status', 'pending')->get();

        foreach ($trips as $trip) {
            $tripId = $trip->id;

            // Assuming the starting date is stored in $trip->start_date
            $startDate = now()->format('F j, Y');  // Format the date to a more user-friendly format

            $title = "Trip Reminder";
            $message = "Your trip #$tripId started today ($startDate). Remember to load your consignments!";


            $notificationData = [
                'notification_type' => 'trip',
                'title' => $title,
                'message' => $message,
                'type' => 1,
                'deliveryType' => $trip->delivery_type == 'single' ? 1 : 2,
                'tripId' => $tripId,
            ];
            $notification = new AndroidNotification($trip->user, $notificationData);
            $trip->user->notify($notification);
        }

        $this->info('Trip reminders sent successfully.');
    }
}
