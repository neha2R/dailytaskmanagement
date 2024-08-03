<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Vehicles;
use App\Notifications\SendPushNotification;
use App\Notifications\WebNotification;
use Carbon\Carbon;
use DateTime;
use Illuminate\Console\Command;

class SendVehicleServiceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicles:send-service-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send vehicle service reminders to admin for vehicles due for service';

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
        $vehicles = Vehicles::get();

        foreach ($vehicles as $key => $vehicle) {
            // Assuming $vehicle is have vehicle information
            $lastServiceDate = $vehicle->services->max('serviceDate');

            if (!$lastServiceDate) {
                $lastServiceDate = $vehicle->registration_date;
            }

            // Convert $lastServiceDate to a Carbon instance
            $lastServiceDate = Carbon::parse($lastServiceDate);

            $serviceTimeDurationMonths = $vehicle->service_time_duration;
            $now = Carbon::now();

            // get next service date using months
            $serviceDate = $lastServiceDate->addMonths($serviceTimeDurationMonths);
            $daysLeft = $serviceDate->diffInDays($now);

            $vehicleNumber = $vehicle->vehicle_number;

            if ($daysLeft == 7) {
                // Send notification 7 days before service is due
                $title = "Service Reminder: {$vehicleNumber}";
                $message = "Vehicle {$vehicleNumber} needs service in 7 days.";
        
                $admin = User::whereNull('role_id')->first();
                $data = [
                    'title' => $title,
                    'message' => $message,
                ];
        
                // Create and send the notification
                $notification = new SendPushNotification($title, $message, $admin, $data);
                $admin->notify($notification);
        
                // Web notification
                $admin->notify(new WebNotification(route('vehicle.vehicleDetails', $vehicle->id), $title, $message));
            } elseif ($daysLeft == 0) {
                // Send notification on the day service is due
                $title = "Service Due Today: {$vehicleNumber}";
                $message = "Vehicle {$vehicleNumber} needs service today. Take immediate action.";
        
                $admin = User::whereNull('role_id')->first();
                $data = [
                    'title' => $title,
                    'message' => $message,
                ];
        
                // Create and send the notification
                $notification = new SendPushNotification($title, $message, $admin, $data);
                $admin->notify($notification);
        
                // Web notification
                $admin->notify(new WebNotification(route('vehicle.vehicleDetails', $vehicle->id), $title, $message));
            } elseif ($daysLeft == -1) {
                // Send notification one day after service is due
                $title = "Service Overdue: {$vehicleNumber}";
                $message = "Vehicle {$vehicleNumber} is overdue for service. Take necessary actions.";
        
                $admin = User::whereNull('role_id')->first();
                $data = [
                    'title' => $title,
                    'message' => $message,
                ];
        
                // Create and send the notification
                $notification = new SendPushNotification($title, $message, $admin, $data);
                $admin->notify($notification);
        
                // Web notification
                $admin->notify(new WebNotification(route('vehicle.vehicleDetails', $vehicle->id), $title, $message));
            } else {
                // Handle other conditions if needed
            }
        }
        $this->info('Vehicles service reminders sent successfully.');
    }
}
