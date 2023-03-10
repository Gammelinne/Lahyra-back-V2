<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Request;
class Test implements ShouldBroadcast
{
    
    use Dispatchable, InteractsWithSockets, SerializesModels;
    Public $data;
    public function __construct($data)
    {
        
        $this->data = [
            'title' => 'Test',
            'data' => $data,
        ];
        
    }   

    public function broadcastOn() : PrivateChannel
    {
        return new PrivateChannel('testchannel');
    }
}