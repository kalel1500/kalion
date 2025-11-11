<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Processes\Infrastructure\Http\Controllers\Ajax;

use Illuminate\Http\JsonResponse;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\CheckableProcessVo;
use Thehouseofel\Kalion\Core\Infrastructure\Http\Controllers\Controller;
use Thehouseofel\Kalion\Core\Infrastructure\Services\Broadcast;
use Thehouseofel\Kalion\Features\Processes\Application\CheckProcessQueueUseCase;
use Thehouseofel\Kalion\Features\Processes\Application\CheckProcessReverbUseCase;
use Thehouseofel\Kalion\Features\Processes\Infrastructure\Events\ProcessStatusChecked;

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
