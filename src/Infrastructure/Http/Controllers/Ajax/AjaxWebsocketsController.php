<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Http\Controllers\Ajax;

use Illuminate\Http\JsonResponse;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Controller;
use Thehouseofel\Kalion\Infrastructure\Events\EventCheckWebsocketsStatus;
use Thehouseofel\Kalion\Infrastructure\Services\Broadcast;
use Throwable;

final class AjaxWebsocketsController extends Controller
{
    /**
     * @throws Throwable
     */
    public function checkService(): JsonResponse
    {
        $res = response_json(true, 'Comprobado servicio websockets');
        return Broadcast::emitEvent($res, new EventCheckWebsocketsStatus());
    }
}
