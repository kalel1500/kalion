<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Response;

use Symfony\Component\HttpFoundation\StreamedResponse;

class ResponseStreamFactory
{
    /**
     * @param callable(ResponseStreamer): void $callback
     */
    public function make(callable $callback, int $status = 200, array $headers = []): StreamedResponse
    {
        // Disable Nginx response buffering by default while allowing callers to override it.
        $headers = array_merge(['X-Accel-Buffering' => 'no'], $headers);

        return response()->stream(function () use ($callback) {
            $callback(new ResponseStreamer());
        }, $status, $headers);
    }
}
