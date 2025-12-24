<?php

namespace App\Events;

use App\Models\Contract;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobPostedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
	public $contract;
	public $user_type;

    /**
     * Create a new event instance.
     *
     * @return void
     */
	public function __construct(Contract $contract, $user_type = 'admin')
	{
		$this->contract = $contract;
		$this->user_type = $user_type;
	}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
