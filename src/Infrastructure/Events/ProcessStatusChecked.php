<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Queue\SerializesModels;

final class ProcessStatusChecked implements ShouldBroadcast
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
            new Channel('process-status'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'ProcessStatusChecked';
    }

    public function broadcastWith(): array
    {
        return [
            'response' => $this->response->getData(),
        ];
    }
}
