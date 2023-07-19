<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class RealTimeMessage implements ShouldBroadcast
{
    use SerializesModels;

  public string $message;
    public string $id2;

    public function __construct(string $message,string $id2)
    {
        $this->message = $message;
        $this->id2 = $id2;
    }
    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.User.' . $this->id2);
    }
     
}
