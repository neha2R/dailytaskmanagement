<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\VehicleDocuments;
use App\Notifications\SendPushNotification;
use App\Notifications\WebNotification;
use Carbon\Carbon;
use DateTime;
use Illuminate\Console\Command;

class SendDocumentExpirationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:send-expiration-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send document expiration reminders to admin for documents expiring soon';

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
        $thresholdDays = 7; // You can adjust this threshold as needed
        $now = now();
        $sevenDaysAfter = $now->copy()->addDays($thresholdDays)->endOfDay();
        $oneDaysBefore = $now->copy()->subDay(1)->startOfDay();

        $documents = VehicleDocuments::where('valid_to', '<=', $sevenDaysAfter)
            ->where('valid_to', '>', $oneDaysBefore)->get();
        foreach ($documents as $document) {
            $documentName = $document->document_name;
            $vehicleNumber = $document->vehicle->vehicle_number;
            $expiryDate = Carbon::parse($document->valid_to); // Convert to Carbon instance
            $expiryDateHours = now()->diffInHours($expiryDate, false);

            $formattedExpiryDate = $expiryDate->format('F j, Y');

            $expiryDateTime = new DateTime($expiryDate);
            $now = new DateTime();

            $daysLeft = $expiryDateTime->diff($now)->format('%a');

            $vehicleId = $document->vehicle_id;

            if ($daysLeft == 7) {
                // Send notification 7 days before expiry
                $title = "Expiring in 7 days: {$documentName} ({$vehicleNumber})";
                $message = "The {$documentName} for vehicle {$vehicleNumber} will expire in 7 days. Expiry date: {$formattedExpiryDate}";
                $admin = User::whereNull('role_id')->first(); // Assuming the admin is the authenticated user
                $data = [
                    'title' => $title,
                    'message' => $message,
                ];

                // Create and send the notification
                $notification = new SendPushNotification($title, $message, $admin, $data);
                $admin->notify($notification);

                // Web notification
                $admin->notify(new WebNotification(route('vehicle.vehicleDetails', $vehicleId), $title, $message));
            } elseif ($daysLeft == 0) {

                if ($expiryDateHours > 0) {
                    // Send notification on the expiry date
                    $title = "Expiring Today: {$documentName} ({$vehicleNumber})";
                    $message = "The {$documentName} for vehicle {$vehicleNumber} expires today. Take immediate action. Expiry date: {$formattedExpiryDate}";
                    $admin = User::whereNull('role_id')->first(); // Assuming the admin is the authenticated user
                    $data = [
                        'title' => $title,
                        'message' => $message,
                    ];

                    // Create and send the notification
                    $notification = new SendPushNotification($title, $message, $admin, $data);
                    $admin->notify($notification);

                    // Web notification
                    $admin->notify(new WebNotification(route('vehicle.vehicleDetails', $vehicleId), $title, $message));
                } else {
                    // Send notification one day after expiry
                    $title = "Expired: {$documentName} ({$vehicleNumber})";
                    $message = "The {$documentName} for vehicle {$vehicleNumber} has expired. Please take necessary actions. Expiry date: {$formattedExpiryDate}";
                    $admin = User::whereNull('role_id')->first(); // Assuming the admin is the authenticated user
                    $data = [
                        'title' => $title,
                        'message' => $message,
                    ];

                    // Create and send the notification
                    $notification = new SendPushNotification($title, $message, $admin, $data);
                    $admin->notify($notification);

                    // Web notification
                    $admin->notify(new WebNotification(route('vehicle.vehicleDetails', $vehicleId), $title, $message));
                }
            } elseif ($daysLeft == -1) {
                // Send notification one day after expiry
                $title = "Expired: {$documentName} ({$vehicleNumber})";
                $message = "The {$documentName} for vehicle {$vehicleNumber} has expired. Please take necessary actions. Expiry date: {$formattedExpiryDate}";
                $admin = User::whereNull('role_id')->first(); // Assuming the admin is the authenticated user
                $data = [
                    'title' => $title,
                    'message' => $message,
                ];

                // Create and send the notification
                $notification = new SendPushNotification($title, $message, $admin, $data);
                $admin->notify($notification);

                // Web notification
                $admin->notify(new WebNotification(route('vehicle.vehicleDetails', $vehicleId), $title, $message));
            }
        }
        $this->info('Document expiration reminders sent successfully.');
    }
}
