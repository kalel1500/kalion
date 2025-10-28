<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Http\Controllers\Ajax;

use Illuminate\Http\JsonResponse;
use Thehouseofel\Kalion\Application\CheckProcessQueueUseCase;
use Thehouseofel\Kalion\Application\CheckProcessReverbUseCase;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters\CheckableProcessVo;
use Thehouseofel\Kalion\Infrastructure\Events\ProcessStatusChecked;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Controller;
use Thehouseofel\Kalion\Infrastructure\Services\Broadcast;

final class AjaxCheckProcessController extends Controller
{
    public function __construct(
        private readonly CheckProcessQueueUseCase  $checkProcessQueueUseCase,
        private readonly CheckProcessReverbUseCase $checkProcessReverbUseCase,
    )
    {
    }

    public function checkQueue(): JsonResponse
    {
        return $this->checkProcessQueueUseCase->__invoke();
    }

    public function broadcastQueueStatus(): JsonResponse
    {
        $response  = $this->checkProcessQueueUseCase->__invoke();
        $broadcast = Broadcast::tryBroadcast(new ProcessStatusChecked(CheckableProcessVo::queue, $response));
        return Broadcast::annotateResponse($response, $broadcast);
    }


    public function checkReverb(): JsonResponse
    {
        return $this->checkProcessReverbUseCase->__invoke();
    }

    public function broadcastReverbStatus(): JsonResponse
    {
        $response  = $this->checkProcessReverbUseCase->__invoke();
        $broadcast = Broadcast::tryBroadcast(new ProcessStatusChecked(CheckableProcessVo::reverb, $response));
        return Broadcast::annotateResponse($response, $broadcast);
    }
}
