<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Application;

use Illuminate\Http\JsonResponse;
use Thehouseofel\Kalion\Infrastructure\Facades\ProcessChecker;

final readonly class CheckProcessQueueUseCase
{
    public function __invoke(): JsonResponse
    {
        $active  = ProcessChecker::checkQueue();
        $message = $active ? __('k::process.queues.active') : __('k::process.queues.inactive');
        return response_json(success: $active, message: $message);
    }
}
