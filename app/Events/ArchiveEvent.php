<?php

namespace App\Events;

use App\Models\Archive;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ArchiveEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Archive $archive;
    public int $ratio;
    public string $eventType;
    /**
     * Create a new event instance.
     */
    public function __construct(Archive $archive, string $eventType)
    {
        $this->archive = $archive;
        $this->eventType = $eventType;
        $this->ratio = intval(($archive->completed_count / $archive->total_count) * 100);
    }

    /**
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'archive' => $this->archive,
            'ratio' => $this->ratio,
            'eventType' => $this->eventType
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return new PrivateChannel($this->archive->type . '.' . $this->archive->user_id);
    }
}
