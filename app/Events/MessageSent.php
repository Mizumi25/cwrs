<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;
use App\Models\User;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $username;
    public $message;
    
    

    /**
     * Create a new event instance.
     */
    public function __construct($user_id, $message)
    {
        $newMessage = new Message();
        $newMessage->user_id = $user_id;
        $newMessage->message = $message;
        $newMessage->save();
        
        $this->message = $message;
        $this->username = User::find($user_id)->name;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new Channel('messages');
    }
}
