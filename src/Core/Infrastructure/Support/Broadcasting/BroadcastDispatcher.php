<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Broadcasting;

use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Symfony\Component\HttpFoundation\Response;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\ResultDto;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Config\Kalion;
use Throwable;

class BroadcastDispatcher
{
    public function dispatch(ShouldBroadcast $event): ResultDto
    {
        try {
            if (Kalion::broadcastingDisabled()) {
                throw new BroadcastException(__('k::process.reverb.inactive'), Response::HTTP_PARTIAL_CONTENT);
            }

            broadcast($event);

            return new ResultDto(success: true, message: __('k::process.reverb.active'));
        } catch (Throwable $e) {
            return new ResultDto(success: false, message: $e->getMessage());
        }
    }
}
