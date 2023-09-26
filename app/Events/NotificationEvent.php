<?php

namespace App\Events;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Notification $notification;

    /**
     * NotificationEvent constructor.
     * @param User $user
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'notification' => $this->notification
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|PrivateChannel|array
     */
    public function broadcastOn(): \Illuminate\Broadcasting\Channel|PrivateChannel|array
    {
        return new PrivateChannel($this->notification->type . '.' . $this->notification->user_id);
    }
}
