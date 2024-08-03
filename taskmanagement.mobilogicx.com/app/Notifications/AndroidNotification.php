<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class AndroidNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $data;

    /**
     * Create a new notification instance.
     *
     * @param mixed $user
     * @param array $data
     */
    public function __construct($user, array $data)
    {
        $this->user = $user;
        $this->data = $data;
        $this->data['click_action'] = "FLUTTER_NOTIFICATION_CLICK";
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['firebase'];
    }

    /**
     * Send the notification via Firebase.
     *
     * @param  mixed  $notifiable
     * @return void
     */
    public function toFirebase($notifiable)
    {
        $this->sendNotification($this->user->device_token, $this->data);
    }

    /**
     * Send notification to the device with Firebase Cloud Messaging (FCM).
     *
     * @param string $token
     * @param array $data
     * @return bool
     */
    protected function sendNotification($token, array $data)
    {
        $fields = [
            'registration_ids' => [$token],
            'data' => $data,
            'notification' => [
                'title' => $data['title'],
                'body' => $data['message'],
            ]
        ];

        $headers = [
            'Authorization: key=' . env('FIREBASE_SERVER_KEY'),
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);

        // Log or handle the result of your notification send here
        return true;
    }
}
