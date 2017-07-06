<?php

namespace Chat\PusherChat\Event;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MemberOnline implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $user;
    public $user2;
    public $NameChannel;
    public $CheckChannel;

    public function __construct( $user, $user2,$NameChannel,$CheckChannel)
    {
        $this->NameChannel = $NameChannel;
        $this->user2 = $user2;
        $this->user = $user;
        $this->CheckChannel= $CheckChannel;

        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        //return new PresenceChannel('chat');
        return new PresenceChannel('memberOnline.1');

    }




}
