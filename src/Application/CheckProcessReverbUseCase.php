<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Application;

use Illuminate\Http\JsonResponse;
use Thehouseofel\Kalion\Infrastructure\Facades\ProcessChecker;

final readonly class CheckProcessReverbUseCase
{
    public function __invoke(): JsonResponse
    {
        $active  = ProcessChecker::checkReverb();
        $message = $active ? __('k::process.reverb.active') : __('k::process.reverb.inactive');
        return response_json(success: $active, message: $message);
    }
}
