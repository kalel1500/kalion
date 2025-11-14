<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Processes\Application;

use Illuminate\Http\JsonResponse;
use Thehouseofel\Kalion\Core\Infrastructure\Facades\ProcessChecker;

final readonly class CheckProcessQueueUseCase
{
    public function __invoke(): JsonResponse
    {
        $active  = ProcessChecker::isRunningQueue();
        $message = $active ? __('k::process.queues.active') : __('k::process.queues.inactive');
        return response_json(success: $active, message: $message);
    }
}
