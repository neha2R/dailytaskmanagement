<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WebNotification extends Notification
{
    public $url,$title,$description;

    public function __construct($url,$title,$description)
    {
        $this->url = $url;
        $this->title = $title;
        $this->description = $description;
    }

    public function toDatabase($notifiable)
    {
        return [
            'url' => $this->url,
            'title' => $this->title,
            'description' => $this->description
        ];
    }
    public function via($notifiable)
    {
        return ['database'];
    }
}
