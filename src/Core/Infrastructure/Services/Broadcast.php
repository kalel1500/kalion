<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Services;

use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Responses\ResponseBroadcastDto;
use Throwable;

final class Broadcast
{
    public static function tryBroadcast(ShouldBroadcast $event): ResponseBroadcastDto
    {
        try {
            if (Kalion::broadcastingDisabled()) {
                throw new BroadcastException(__('k::process.reverb.inactive'), Response::HTTP_PARTIAL_CONTENT);
            }
            broadcast($event);
            return new ResponseBroadcastDto(success: true, message: __('k::process.reverb.active'));
        } catch (Throwable $e) {
            return new ResponseBroadcastDto(success: false, message: $e->getMessage());
        }
    }

    public static function annotateResponse(JsonResponse $response, ResponseBroadcastDto $broadcast): JsonResponse
    {
        $data                         = $response->getData(true);
        $data['data']['broadcasting'] = $broadcast->toArray();
        $response->setData($data);
        return $response;
    }
}
