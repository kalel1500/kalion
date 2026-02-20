<?php

declare(strict_types=1);

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as RequestF;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\ExceptionContextDto;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\EnvVo;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Config\Redirect\RedirectAfterLogin;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Config\Redirect\RedirectDefaultPath;
use function Illuminate\Filesystem\join_paths;

if (! function_exists('dropdown_is_open')) {
    function dropdown_is_open(string $htmlLinks): bool
    {
        $currentUrl = RequestF::fullUrl();
        // Expresión regular para encontrar todos los href en los enlaces
        preg_match_all('/<a\s+href=["\']([^"\']+)["\']/', $htmlLinks, $matches);
        $hrefs = $matches[1]; // $matches[1] contiene todos los href encontrados
        return in_array($currentUrl, $hrefs); // Comprueba si la URL actual está en la lista
    }
}

if (! function_exists('debug_is_active')) {
    function debug_is_active(): bool
    {
        return config('app.debug');
    }
}

if (! function_exists('filter_valid_emails')) {
    function filter_valid_emails(array|string $emails): array
    {
        if (is_string($emails)) {
            $emails = explode(',', $emails);
        }
        return collect($emails)
            ->map(function ($value) {
                return trim($value);
            })
            ->filter(function ($value) {
                return filter_var($value, FILTER_VALIDATE_EMAIL);
            })
            ->all();
    }
}

if (! function_exists('url_contains_ajax')) {
    function url_contains_ajax(): bool
    {
        return (str_contains(URL::current(), '/ajax/'));
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

if (! function_exists('str_snake')) {
    function str_snake($str): string
    {
        return Str::snake($str);
    }
}

if (! function_exists('array_has_only_arrays')) {
    function array_has_only_arrays(array $array): bool
    {
        $filtered = Arr::where($array, function ($value, $key) {
            return ! is_array($value);
        });
        return (count($filtered) === 0);
    }
}

if (! function_exists('safe_route')) {
    function safe_route(?string $name, string $default = null): ?string
    {
        $fallback = match ($default) {
            null    => null,
            '#'     => '#',
            default => url($default),
        };

        try {
            return is_null($name) ? $fallback : route($name);
        } catch (\Symfony\Component\Routing\Exception\RouteNotFoundException $exception) {
            return $fallback;
        }
    }
}

if (! function_exists('concat_fields_with')) {
    function concat_fields_with(array $fields = ['name', 'code'], string $separator = 'or'): string
    {
        $separator = __('k::art.' . $separator);
        $fields    = array_map(fn(string $item): string => '"' . ucfirst(__('k::field.' . $item)) . '"', $fields);
        return implode(" $separator ", $fields);
    }
}

if (! function_exists('get_html_laravel_debug_stack_trace')) {
    function get_html_laravel_debug_stack_trace(Request $request, Throwable $exception): string
    {
        return app()->make(\Illuminate\Foundation\Exceptions\Renderer\Renderer::class)->render($request, $exception);
    }
}

if (! function_exists('redirect_default_to')) {
    function redirect_default_to(Request $request): ?string
    {
        return app(RedirectDefaultPath::class)->redirectTo($request);
    }
}

if (! function_exists('redirect_after_login_to')) {
    function redirect_after_login_to(Request $request): ?string
    {
        return app(RedirectAfterLogin::class)->redirectTo($request);
    }
}

if (! function_exists('app_url')) {
    function app_url(): string
    {
        return url('/');
    }
}

if (! function_exists('default_url')) {
    function default_url(): string
    {
        $defaultUrl = redirect_default_to(request());

        if ($defaultUrl === app_url()) {
            abort_d(500, __('k::error.default_url_equals_to_app_url'));
        }

        return $defaultUrl;
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

if (! function_exists('log_error')) {
    function log_error(string $message): void
    {
        Log::error($message);
    }
}

if (! function_exists('log_error_on')) {
    function log_error_on(string $channel, string $message): void
    {
        Log::channel($channel)->error($message);
    }
}

if (! function_exists('log_error_on_queues')) {
    function log_error_on_queues(string $message): void
    {
        Log::channel('queues')->error($message);
    }
}

if (! function_exists('log_error_on_loads')) {
    function log_error_on_loads(string $message): void
    {
        Log::channel('loads')->error($message);
    }
}

if (! function_exists('vite_asset')) {
    function vite_asset(string $asset): string
    {
        try {
            return \Illuminate\Support\Facades\Vite::asset($asset);
        } catch (\Illuminate\Foundation\ViteException $exception) {
            return $exception->getMessage();
        }
    }
}

if (! function_exists('get_environment')) {
    function get_environment(): EnvVo
    {
        return EnvVo::from(config('app.env'));
    }
}

if (! function_exists('env_isTesting')) {
    function env_isTesting(): bool
    {
        return get_environment()->isTesting();
    }
}

if (! function_exists('env_isLocal')) {
    function env_isLocal(): bool
    {
        return get_environment()->isLocal();
    }
}

if (! function_exists('env_isPre')) {
    function env_isPre(): bool
    {
        return get_environment()->isPre();
    }
}

if (! function_exists('env_isProd')) {
    function env_isProd(): bool
    {
        return get_environment()->isProd();
    }
}

if (! function_exists('current_route_name_is')) {
    function current_route_name_is(string $name): bool
    {
        return Route::currentRouteName() === $name;
    }
}
