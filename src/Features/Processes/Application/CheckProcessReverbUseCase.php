<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Processes\Application;

use Illuminate\Http\JsonResponse;
use Thehouseofel\Kalion\Core\Infrastructure\Laravel\Facades\Process;

final readonly class CheckProcessReverbUseCase
{
    public function __invoke(): JsonResponse
    {
        $active  = Process::isRunningReverb();
        $message = $active ? __('k::process.reverb.active') : __('k::process.reverb.inactive');
        return response_json(success: $active, message: $message);
    }
}
