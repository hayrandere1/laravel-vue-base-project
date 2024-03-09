<?php

namespace App\Events;

use App\Models\Session;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminReportEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $session;

    /**
     * Create a new event instance.
     */
    public function __construct($session = [])
    {
        $this->session = $session;
    }

    public function broadcastWith(): array
    {
        return [
            'session' => $this->session,
        ];
    }

    /**
     * @return Channel|PrivateChannel|array
     */
    public function broadcastOn(): \Illuminate\Broadcasting\Channel|PrivateChannel|array
    {
        return new PrivateChannel('admin');
    }
}
