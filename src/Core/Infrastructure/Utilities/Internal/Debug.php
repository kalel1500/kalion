<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Internal;

use Illuminate\Foundation\Exceptions\Renderer\Renderer;
use Illuminate\Http\Request;
use Throwable;

/**
 * @internal This class is intended for internal package usage only.
 */
final class Debug
{
    public static function renderLaravelDebugStackTrace(Request $request, Throwable $exception): string
    {
        return app()->make(Renderer::class)->render($request, $exception);
    }
}
