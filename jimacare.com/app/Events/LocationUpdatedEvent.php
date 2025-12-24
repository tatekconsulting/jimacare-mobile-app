<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LocationUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bookingId;
    public $userId;
    public $recipientId;
    public $lat;
    public $long;
    public $etaMinutes;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($bookingId, $userId, $recipientId, $lat, $long, $etaMinutes = null)
    {
        $this->bookingId = $bookingId;
        $this->userId = $userId;
        $this->recipientId = $recipientId;
        $this->lat = $lat;
        $this->long = $long;
        $this->etaMinutes = $etaMinutes;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('booking.' . $this->bookingId . '.location');
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'user_id' => $this->userId,
            'lat' => $this->lat,
            'long' => $this->long,
            'eta_minutes' => $this->etaMinutes,
            'timestamp' => now()->toIso8601String()
        ];
    }
}

