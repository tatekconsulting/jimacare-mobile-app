<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $from, $to, $message;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $from, User $to, Message $message)
    {
    	$this->from     = $from;
	    $this->to       = $to;
    	$this->message  = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
	    return new PrivateChannel('inbox-' . $this->to->id);
    }

	public function broadcastWith () {
		return [
			'id'        => $this->from->id,
			'name'      => ($this->from->firstname ?? '') . '' . ($this->from->lastname[0] ?? ''),
			'profile'   => asset($this->from->profile ?? 'img/undraw_profile.svg'),
			'message'   => $this->message->message ?? '',
			'sent_at'   => $this->message->created_at->format('d/m/Y \a\t H:i')
		];
	}
}
