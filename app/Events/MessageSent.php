<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;


class MessageSent implements ShouldBroadcast
{
    public $message;
    public $userId;


    public $locations;

    public function __construct($message, $locations, $userId)
    {
        $this->message = $message;
        $this->locations = $locations;
        $this->userId = $userId;

        Log::info('MESS', [
            'id' => $this->userId,
            'locations' => $this->locations
        ]);
    }


    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->userId);
        // return new PrivateChannel('user.6');
        // return new PrivateChannel('ptest');
        // return new PresenceChannel('ptest');

        // return new Channel('ptest');
    }
}



// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

// class MessageSent implements ShouldBroadcast
// {
//     public $message;
//     public $userId;

//     public function __construct($message, $userId)
//     {
//         $this->message = $message;
//         $this->userId = $userId;
//     }

//     public function broadcastOn()
//     {
//         return new PrivateChannel('user.'.$this->userId);
//     }
// }