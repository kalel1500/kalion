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
    public static function renderLaravelDebugStackTrace(Request $request, Throwable $exception, array $debugData = []): string
    {
        $html = app()->make(Renderer::class)->render($request, $exception);

        if ($debugData === []) {
            return $html;
        }

        $panel = view('kal::pages.exceptions.debug-data', compact('debugData'))->render();

        return str_contains($html, '</body>')
            ? str_replace('</body>', $panel . '</body>', $html)
            : $html . $panel;
    }
}
