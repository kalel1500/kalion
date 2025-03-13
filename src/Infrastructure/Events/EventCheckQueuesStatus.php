<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Queue\SerializesModels;

final class EventCheckQueuesStatus implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        private JsonResponse $response
    )
    {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('check-queues-status'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'EventCheckQueuesStatus';
    }

    public function broadcastWith(): array
    {
        return [
            'response' => $this->response->getData(),
        ];
    }
}
