<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services;

use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

final class WebsocketsService
{
    public static function emitEvent(JsonResponse $response, ShouldBroadcast $instanceEvent): JsonResponse
    {
        try {
            if (!broadcastingIsActive()) throw new BroadcastException(__('h::service.websockets.inactive'), Response::HTTP_PARTIAL_CONTENT);
            broadcast($instanceEvent);
            $data = $response->getData(true);
            $data['data']['broadcasting'] = ['success' => true, 'message' => 'Servicio websockets levantado'];
            $response->setData($data);
            return $response;
        } catch (Throwable $e) {
            $data = $response->getData(true);
            $data['data']['broadcasting'] = ['success' => false, 'message' => $e->getMessage()];
            $response->setData($data);
            return $response;
        }
    }

    public static function emitEventSimple(ShouldBroadcast $instanceEvent): void
    {
        try {
            if (!broadcastingIsActive()) throw new BroadcastException(__('h::service.websockets.inactive'), Response::HTTP_PARTIAL_CONTENT);
            broadcast($instanceEvent);
        } catch (BroadcastException $e) {
            //
        }
    }
}
