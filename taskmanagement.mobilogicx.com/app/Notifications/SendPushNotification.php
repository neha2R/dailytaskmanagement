<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class SendPushNotification extends Notification
{
    protected $title;
    protected $message;
    protected $user;
    protected $data;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($title, $message, $user, $data)
    {
        $this->title = $title;
        $this->message = $message;
        $this->user = $user;
        $this->data = $data;
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

    public function toFirebase($notifiable)
    {
        $deviceTokens[] = $this->user->device_token;

        $firebaseMessage = (new FirebaseMessage)
            ->withTitle($this->title)
            ->withBody($this->message)
            ->withPriority('high')
            ->asMessage($deviceTokens, $this->data);
        return $firebaseMessage;
    }
}