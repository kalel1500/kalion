<?php

declare(strict_types=1);

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\ExceptionContextDto;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\ResultDto;
use Thehouseofel\Kalion\Core\Infrastructure\Kalion;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Facades\Broadcast;
use function Illuminate\Filesystem\join_paths;

if (! function_exists('kalion')) {
    function kalion(): Kalion
    {
        return app(Kalion::class);
    }
}

if (! function_exists('debug_enabled')) {
    function debug_enabled(): bool
    {
        return app()->hasDebugModeEnabled();
    }
}

if (! function_exists('response_json')) {
    function response_json(bool $success, string $message, array|object|string|null $data = null, int $responseCode = 200): JsonResponse
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data'    => $data
        ], $responseCode);
    }
}

if (! function_exists('response_json_with')) {
    function response_json_with(array $data = [], int $responseCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json($data, $responseCode);
    }
}

if (! function_exists('response_json_error')) {
    function response_json_error(Throwable $e, bool $throwInDebugMode = true): JsonResponse
    {
        // INFO kalel1500 - mi_estructura_de_respuesta
        $exceptionData = ExceptionContextDto::from($e);
        return response()->json($exceptionData->toArray($throwInDebugMode), $exceptionData->statusCode);
    }
}

if (! function_exists('src_path')) {
    /**
     * Get the path to the application folder.
     */
    function src_path(string $path = ''): string
    {
        $srcPath = base_path('src');
        return join_paths($srcPath, $path);
    }
}

if (! function_exists('safe_route')) {
    function safe_route(?string $name, string $default = null, array $params = []): ?string
    {
        $fallback = match ($default) {
            null    => null,
            '#'     => '#',
            default => url($default),
        };

        try {
            return is_null($name) ? $fallback : route($name, $params);
        } catch (\Symfony\Component\Routing\Exception\RouteNotFoundException $exception) {
            return $fallback;
        }
    }
}

if (! function_exists('app_url')) {
    function app_url(): string
    {
        return url('/');
    }
}

if (! function_exists('log_if_fail')) {
    function log_if_fail(string $errorPrefix, callable $callback, ?string $logChannel = null): void
    {
        try {
            $callback();
        } catch (Throwable $exception) {
            Log::channel($logChannel)->error($errorPrefix . $exception->getMessage());
        }
    }
}

if (! function_exists('vite_asset')) {
    function vite_asset(string $asset, bool $silent = false): ?string
    {
        try {
            return \Illuminate\Support\Facades\Vite::asset($asset);
        } catch (\Illuminate\Foundation\ViteException $exception) {
            if (! $silent) {
                throw $exception;
            }

            return null;
        }
    }
}

if (! function_exists('safe_broadcast')) {
    function safe_broadcast(ShouldBroadcast $event): ResultDto
    {
        return Broadcast::dispatch($event);
    }
}
