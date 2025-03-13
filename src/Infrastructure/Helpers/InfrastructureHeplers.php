<?php

declare(strict_types=1);

use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Collection as CollectionE;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request as RequestF;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\ComponentAttributeBag;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\ExceptionContextDo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters\EnvVo;

if (!function_exists('dropdown_is_open')) {
    function dropdown_is_open(string $htmlLinks): bool
    {
        $currentUrl = RequestF::fullUrl();
        // Expresión regular para encontrar todos los href en los enlaces
        preg_match_all('/<a\s+href=["\']([^"\']+)["\']/', $htmlLinks, $matches);
        $hrefs = $matches[1]; // $matches[1] contiene todos los href encontrados
        return in_array($currentUrl, $hrefs); // Comprueba si la URL actual está en la lista
    }
}

if (! function_exists('get_environment')) {
    function get_environment(): string
    {
        $env = new EnvVo(config('app.env'));
        return $env->value();
    }
}

if (! function_exists('get_environment_real')) {
    function get_environment_real(): ?string
    {
        return config('kalion.real_env_in_tests');
    }
}

if (! function_exists('env_is_prod')) {
    function env_is_prod(): bool
    {
        return get_environment() === EnvVo::production;
    }
}

if (! function_exists('env_is_pre')) {
    function env_is_pre(): bool
    {
        return get_environment() === EnvVo::preproduction;
    }
}

if (! function_exists('env_is_local')) {
    function env_is_local(): bool
    {
        return get_environment() === EnvVo::local;
    }
}

if (! function_exists('env_is_not_prod')) {
    function env_is_not_prod(): bool
    {
        return !env_is_prod();
    }
}

if (! function_exists('env_is_not_pre')) {
    function env_is_not_pre(): bool
    {
        return !env_is_pre();
    }
}

if (! function_exists('env_is_not_local')) {
    function env_is_not_local(): bool
    {
        return !env_is_local();
    }
}

if (! function_exists('env_is_test')) {
    function env_is_test(): bool
    {
        return config('app.env') === EnvVo::testing;
    }
}

if (! function_exists('debug_is_active')) {
    function debug_is_active(): bool {
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
            ->map(function ($value) {return trim($value);})
            ->filter(function ($value) {return filter_var($value, FILTER_VALIDATE_EMAIL);})
            ->all();
    }
}

if (!function_exists('url_contains_ajax')) {
    function url_contains_ajax(): bool
    {
        return (str_contains(URL::current(), '/ajax/'));
    }
}

if (! function_exists('response_json')) {
    function response_json(bool $success, string $message, ?array $data = null, int $responseCode = 200): JsonResponse
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data
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
        $exceptionData = ExceptionContextDo::from($e);
        return response()->json($exceptionData->toArray($throwInDebugMode), $exceptionData->getStatusCode());
    }
}

if (! function_exists('src_path')) {
    /**
     * Get the path to the application folder.
     */
    function src_path(): string
    {
        return base_path().DIRECTORY_SEPARATOR.'src';
    }
}

if (!function_exists('str_snake')) {
    function str_snake($str): string
    {
        return Str::snake($str);
    }
}

if (!function_exists('coll_first')) {
    function coll_first(array $array, callable $callback = null, $default = null)
    {
        return collect($array)->first($callback, $default);
    }
}

if (!function_exists('coll_last')) {
    function coll_last(array $array, callable $callback = null, $default = null)
    {
        return collect($array)->last($callback, $default);
    }
}

if (!function_exists('coll_where')) {
    function coll_where(array $array, $key, $operator = null, $value = null)
    {
        if (func_num_args() === 2) {
            $value = true;
            $operator = '=';
        }
        if (func_num_args() === 3) {
            $value = $operator;
            $operator = '=';
        }
        return collect($array)->where($key, $operator, $value);
    }
}

if (!function_exists('coll_where_in')) {
    function coll_where_in(array $array, $key, $values, $strict = false)
    {
        return collect($array)->whereIn($key, $values, $strict);
    }
}

if (!function_exists('coll_contains')) {
    function coll_contains($array, $key, $operator = null, $value = null): bool
    {
        $coll = collect($array);
        if (func_num_args() === 2) {
            return $coll->contains($key);
        }
        if (func_num_args() === 3) {
            return $coll->contains($key, $operator);
        }
        return $coll->contains($key, $operator, $value);
    }
}

if (!function_exists('coll_unique')) {
    function coll_unique(array $array, $key = null, $strict = false)
    {
        return collect($array)->unique($key, $strict);
    }
}

if (!function_exists('coll_filter')) {
    function coll_filter(array $array, callable $callback = null)
    {
        return collect($array)->filter($callback);
    }
}

if (!function_exists('coll_sort_by')) {
    function coll_sort_by(array $array, $callback, $options = SORT_REGULAR, $descending = false)
    {
        return collect($array)->sortBy($callback, $options, $descending);
    }
}

if (!function_exists('coll_sort')) {
    function coll_sort(array $array, $callback = null)
    {
        return collect($array)->sort($callback);
    }
}

if (!function_exists('coll_sort_desc')) {
    function coll_sort_desc(array $array, $options = SORT_REGULAR)
    {
        return collect($array)->sortDesc($options);
    }
}

if (!function_exists('coll_group_by')) {
    function coll_group_by(array $array, $groupBy, $preserveKeys = false)
    {
        return collect($array)->groupBy($groupBy, $preserveKeys);
    }
}

if (!function_exists('coll_select')) {
    function coll_select(array $array, $keys)
    {
//        $keys = is_array($keys) ? $keys : func_get_args();
        return collect($array)->map(function ($item) use ($keys) {
            return collect($item)->only($keys)->toArray();
        });
    }
}

if (!function_exists('coll_flatten')) {
    function coll_flatten(array $array, $depth = INF)
    {
        return collect($array)->flatten($depth);
    }
}

if (!function_exists('coll_take')) {
    function coll_take(array $array, int $limit)
    {
        return collect($array)->take($limit);
    }
}

if (!function_exists('array_has_only_arrays')) {
    function array_has_only_arrays(array $array): bool
    {
        $filtered = Arr::where($array, function ($value, $key) {
            return !is_array($value);
        });
        return (count($filtered) === 0);
    }
}

if (! function_exists('broadcasting_is_active')) {
    function broadcasting_is_active(): bool
    {
        return (bool)config('kalion.broadcasting_enabled');
    }
}

if (! function_exists('get_url_from_route')) {
    function get_url_from_route($route): string
    {
        try {
            return is_null($route) ? '#' : route($route);
        } catch (\Symfony\Component\Routing\Exception\RouteNotFoundException $exception) {
            return '#';
        }
    }
}

if (!function_exists('concat_fields_with')) {
    function concat_fields_with(array $fields = ['name', 'code'], string $separator = 'or'): string
    {
        $separator = __('h::art.'.$separator);
        $fields = array_map(fn(string $item): string => '"'.ucfirst(__('h::field.'.$item)).'"', $fields);
        return implode(" $separator ", $fields);
    }
}

if (!function_exists('get_class_user_model')) {
    function get_class_user_model(): string // |\Illuminate\Foundation\Auth\User
    {
        return config('auth.providers.users.model');
    }
}

if (!function_exists('get_class_user_entity')) {
    function get_class_user_entity(): string
    {
        return config('kalion_auth.entity_class');
    }
}

if (!function_exists('get_html_laravel_debug_stack_trace')) {
    function get_html_laravel_debug_stack_trace(Request $request, Throwable $exception): string
    {
        return app()->make(\Illuminate\Foundation\Exceptions\Renderer\Renderer::class)->render($request, $exception);
    }
}

if (!function_exists('app_url')) {
    function app_url(): string
    {
        return rtrim(config('app.url'), '/');
    }
}

if (!function_exists('default_route')) {
    function default_route(): string
    {
        return '/' . ltrim(config('kalion.default_route'), '/');
    }
}

if (!function_exists('default_url')) {
    function default_url(): string
    {
        return app_url() . default_route();
    }
}

if (!function_exists('save_execute')) {
    function save_execute(string $errorPrefix, string $logChannel, callable $callback): void
    {
        try {
            $callback();
        } catch (Throwable $exception) {
            Log::channel($logChannel)->error($errorPrefix.$exception->getMessage());
        }
    }
}
