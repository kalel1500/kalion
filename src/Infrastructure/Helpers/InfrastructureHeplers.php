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
use Illuminate\Support\Facades\Request as RequestF;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\ComponentAttributeBag;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\ExceptionContextDo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters\EnvVo;
use Thehouseofel\Kalion\Infrastructure\Helpers\MyCarbon;

if (!function_exists('showActiveClass')) {
    /**
     * @deprecated This function is deprecated and will be removed in a future version.
     */
    function showActiveClass(string $value, ?string $paramName = null, ?string $paramValue = null): bool
    {
        trigger_error('The function showActiveClass() is deprecated.', E_USER_DEPRECATED);
        $routeName = Route::currentRouteName();
        $param = Route::current()->parameters[$paramName] ?? null;
        if (is_null($paramName)) {
            return ($routeName === $value);
        } elseif (is_null($paramValue)) {
            return ($routeName === $value && !isset($param));
        } else {
            return ($routeName === $value && isset($param) && $param === $paramValue);
        }
    }
}

if (!function_exists('isRouteActive')) {
    function isRouteActive($url): bool
    {
        return RequestF::fullUrl() === $url;
    }
}

if (!function_exists('dropdownIsOpen')) {
    function dropdownIsOpen($htmlLinks): bool
    {
        $currentUrl = RequestF::fullUrl();
        // Expresión regular para encontrar todos los href en los enlaces
        preg_match_all('/<a\s+href=["\']([^"\']+)["\']/', $htmlLinks, $matches);
        $hrefs = $matches[1]; // $matches[1] contiene todos los href encontrados
        return in_array($currentUrl, $hrefs); // Comprueba si la URL actual está en la lista
    }
}

if (!function_exists('currentRouteNamed')) {
    function currentRouteNamed($name, array $params = null): bool
    {
        // Comprobar si el nombre de la ruta actual coincide con la recibida
        $currentRouteNamed = Route::currentRouteNamed($name);

        // Si no recibimos parámetros devolvemos el resultado anterior
        if (is_null($params)) {
            return $currentRouteNamed;
        }

        // Obtener los parámetros de la ruta actual
        $currentRouteParams = Route::getCurrentRoute()->parameters();

        // Caso 1: solo keys (valores vacíos o con índices numéricos)
        $onlyKeys = array_filter($params, 'is_int', ARRAY_FILTER_USE_KEY);
        if (!empty($onlyKeys)) {
            // Si alguna de las keys del input no está en el array origen
            if (!Arr::has($currentRouteParams, $onlyKeys)) { // array_diff($onlyKeys, array_keys($currentRouteParams))
                return false;
            }
        }

        // Caso 2: keys con valores
        $keysWithValues = array_filter($params, 'is_string', ARRAY_FILTER_USE_KEY);
        if (!empty($keysWithValues)) {
            // Si alguna de las claves o valores no coinciden
            $originSubset = Arr::only($currentRouteParams, array_keys($keysWithValues));
            if ($originSubset !== $keysWithValues) { // array_diff_assoc($keysWithValues, array_intersect_key($currentRouteParams, $keysWithValues))
                return false;
            }
        }

        return $currentRouteNamed;
    }
}

if (!function_exists('getRouteInput')) {
    function getRouteInput($value)
    {
        return Route::current()->parameter($value) ?? null;
    }
}

if (!function_exists('routeContains')) {
    function routeContains($wordToSearch): bool
    {
        $routeName = Route::currentRouteName();
        return str_contains($routeName, $wordToSearch);
    }
}

if (!function_exists('createRandomString')) {
    function createRandomString($minNumberChars, $maxNumberChars): string
    {
        $faker = Faker::create();
        $positionsInWhichAddSpaces = [5, 10, 15, 20, 25, 30, 35, 40, 45];
        $numberChars = $faker->numberBetween($minNumberChars, $maxNumberChars);
        $string = '';
        for ($i=0; $i<=$numberChars; $i++) {
            $string .= (in_array($i, $positionsInWhichAddSpaces)) ? ' ' : $faker->randomLetter;
        }
        return $string;
    }
}

/*if (!function_exists('checkObjectProperties')) {
    function checkObjectProperties($object, $arrayProperties, $preventException = false)
    {
        $checkProperties = collect([]);
        foreach ($arrayProperties as $prop) {
            $checkProperties->push(property_exists($object, $prop));
        }

        if ($checkProperties->contains(false)) {
            if ($preventException) return false;
            throw new \Exception(__('The object does not meet the required properties'));
        }

        return true;
    }
}*/

if (!function_exists('formatStringDatetimeTo')) {
    function formatStringDatetimeTo($datetime, $format = 'Y-m-d\TH:i'): ?string
    {
        return MyCarbon::stringToformat($datetime, $format);
    }
}

if (!function_exists('compareDates')) {
    function compareDates($date1, $operator, $date2)
    {
        return MyCarbon::compare($date1, $operator, $date2);
    }
}

//if (!function_exists('upsert_getArraysForExcept')) {
//    /**
//     * @param array $arrayData
//     * @param array $arrayKeys
//     * @param array $exceptColumns
//     * @param bool $deleteTimestamps
//     * @return MyUpsert
//     */
//    function upsert_getArraysForExcept($arrayData, $arrayKeys, $exceptColumns, $deleteTimestamps = true)
//    {
//        $upsert = new MyUpsert($arrayData, $arrayKeys, $exceptColumns, $deleteTimestamps);
//        return $upsert;
//
//        /*$arrayUpdate = array_diff(array_keys($arrayData[0]), $exceptColumns);
//        return [
//            'arrayData' => $arrayData,
//            'arrayKeys' => $arrayKeys,
//            'arrayUpdate' => $arrayUpdate,
//        ];*/
//    }
//}

//if (!function_exists('upsert_ExecuteForExcept')) {
//    /**
//     * @param Builder $builder
//     * @param $arrayData
//     * @param $arrayKeys
//     * @param $exceptColumns
//     * @param bool $deleteTimestamps
//     */
//    function upsert_executeForExcept(Builder $builder, $arrayData, $arrayKeys, $exceptColumns, $deleteTimestamps = true)
//    {
//        $upsert = new MyUpsert($arrayData, $arrayKeys, $exceptColumns, $deleteTimestamps);
//        $upsert->executeUpsert($builder);
//    }
//}

if (! function_exists('collectE')) {
    /**
     * @deprecated This function is deprecated and will be removed in a future version.
     *
     * Create a collection from the given value.
     */
    function collectE(mixed $value = null): CollectionE
    {
        trigger_error('The function collectE() is deprecated.', E_USER_DEPRECATED);
        return new CollectionE($value);
    }
}

if (! function_exists('collectionContains')) {
    /**
     * @deprecated This function is deprecated and will be removed in a future version.
     */
    function collectionContains(Collection $collection, $keySearch, $valueSearch): bool
    {
        trigger_error('The function collectionContains() is deprecated.', E_USER_DEPRECATED);
        $filtered = $collection->filter(function ($value, $key) use ($keySearch, $valueSearch) {
//        return strtolower($value->$keySearch) == strtolower($valueSearch);
            return stristr(strtolower($value->$keySearch), strtolower($valueSearch)) == true;
        });
        return $filtered->isNotEmpty();
    }
}

if (! function_exists('myCarbon')) {
    /**
     * @deprecated This function is deprecated and will be removed in a future version.
     */
    function myCarbon(): MyCarbon
    {
        trigger_error('The function myCarbon() is deprecated.', E_USER_DEPRECATED);
        return new MyCarbon();
    }
}

if (! function_exists('getEnvironment')) {
    function getEnvironment(): string
    {
        $env = new EnvVo(config('app.env'));
        return $env->value();
    }
}

if (! function_exists('getEnvironmentReal')) {
    function getEnvironmentReal(): ?string
    {
        return config('kalion.real_env_in_tests');
    }
}

if (! function_exists('envIsPorduction')) {
    function envIsPorduction(): bool
    {
        return getEnvironment() === EnvVo::production;
    }
}

if (! function_exists('envIsPre')) {
    function envIsPre(): bool
    {
        return getEnvironment() === EnvVo::preproduction;
    }
}

if (! function_exists('envIsLocal')) {
    function envIsLocal(): bool
    {
        return getEnvironment() === EnvVo::local;
    }
}

if (! function_exists('envIsNotPorduction')) {
    function envIsNotPorduction(): bool
    {
        return !envIsPorduction();
    }
}

if (! function_exists('envIsNotPre')) {
    function envIsNotPre(): bool
    {
        return !envIsPre();
    }
}

if (! function_exists('envIsNotLocal')) {
    function envIsNotLocal(): bool
    {
        return !envIsLocal();
    }
}

if (! function_exists('envIsTest')) {
    function envIsTest(): bool
    {
        return config('app.env') === EnvVo::testing;
    }
}

if (! function_exists('appIsInDebugMode')) {
    /**
     * @deprecated This function is deprecated and will be removed in a future version.
     */
    function appIsInDebugMode(): bool {
        trigger_error('The function appIsInDebugMode() is deprecated. Use debugIsActive().', E_USER_DEPRECATED);
        return config('app.debug');
    }
}

if (! function_exists('debugIsActive')) {
    function debugIsActive(): bool {
        return config('app.debug');
    }
}

if (! function_exists('formatArrayOfEmailsToSendMail')) {
    function formatArrayOfEmailsToSendMail($array): array
    {
        return collect($array)
            ->map(function ($value) {return (verifyEmail($value)) ? ['name' => null, 'email' => $value] : null;})
            ->filter(function ($value) {return !is_null($value);})
            ->all();
    }
}

if (! function_exists('getGoodEmailsFromArray')) {
    function getGoodEmailsFromArray($array): array
    {
        if (is_string($array)) {
            $array = explode(',', $array);
        }
        return collect($array)
            ->map(function ($value) {return trim($value);})
            ->filter(function ($value) {return verifyEmail($value);})
            ->all();
    }
}

if (!function_exists('isValidationException')) {
    function isValidationException(Throwable $e): bool
    {
        return ($e instanceof ValidationException);
    }
}

if (!function_exists('urlContainsAjax')) {
    function urlContainsAjax(): bool
    {
        return (str_contains(URL::current(), '/ajax/'));
    }
}

if (! function_exists('responseJson')) {
    function responseJson(bool $success, string $message, ?array $data = null, int $responseCode = 200): JsonResponse
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ], $responseCode);
    }
}

if (! function_exists('responseJsonWith')) {
    function responseJsonWith(array $data = [], int $responseCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json($data, $responseCode);
    }
}

if (! function_exists('responseJsonError')) {
    function responseJsonError(Throwable $e, bool $throwInDebugMode = true): JsonResponse
    {
        // INFO kalel1500 - mi_estructura_de_respuesta
        $exceptionData = ExceptionContextDo::from($e);
        return response()->json($exceptionData->toArray($throwInDebugMode), $exceptionData->getStatusCode());
    }
}

if (!function_exists('myOptional')) {
    function myOptional($value)
    {
        return optional($value);
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

if (!function_exists('dbTransaction')) {
    function dbTransaction($callback)
    {
        DB::transaction($callback);
    }
}

if (!function_exists('strToSnake')) {
    function strToSnake($str): string
    {
        return Str::snake($str);
    }
}

if (!function_exists('arrSort')) {
    function arrSort($array, $callback): array
    {
        return Arr::sort($array, $callback);
    }
}

if (!function_exists('myCollect')) {
    function myCollect(array $array)
    {
        return collect($array);
    }
}

if (!function_exists('collFirst')) {
    function collFirst(array $array, callable $callback = null, $default = null)
    {
        return collect($array)->first($callback, $default);
    }
}

if (!function_exists('collLast')) {
    function collLast(array $array, callable $callback = null, $default = null)
    {
        return collect($array)->last($callback, $default);
    }
}

if (!function_exists('collWhere')) {
    function collWhere(array $array, $key, $operator = null, $value = null)
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

if (!function_exists('collWhereIn')) {
    function collWhereIn(array $array, $key, $values, $strict = false)
    {
        return collect($array)->whereIn($key, $values, $strict);
    }
}

if (!function_exists('collContains')) {
    function collContains($array, $key, $operator = null, $value = null): bool
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

if (!function_exists('collUnique')) {
    function collUnique(array $array, $key = null, $strict = false)
    {
        return collect($array)->unique($key, $strict);
    }
}

if (!function_exists('collFilter')) {
    function collFilter(array $array, callable $callback = null)
    {
        return collect($array)->filter($callback);
    }
}

if (!function_exists('collSortBy')) {
    function collSortBy(array $array, $callback, $options = SORT_REGULAR, $descending = false)
    {
        return collect($array)->sortBy($callback, $options, $descending);
    }
}

if (!function_exists('collSort')) {
    function collSort(array $array, $callback = null)
    {
        return collect($array)->sort($callback);
    }
}

if (!function_exists('collSortDesc')) {
    function collSortDesc(array $array, $options = SORT_REGULAR)
    {
        return collect($array)->sortDesc($options);
    }
}

if (!function_exists('collGroupBy')) {
    function collGroupBy(array $array, $groupBy, $preserveKeys = false)
    {
        return collect($array)->groupBy($groupBy, $preserveKeys);
    }
}

if (!function_exists('collSelect')) {
    function collSelect(array $array, $keys)
    {
//        $keys = is_array($keys) ? $keys : func_get_args();
        return collect($array)->map(function ($item) use ($keys) {
            return collect($item)->only($keys)->toArray();
        });
    }
}

if (!function_exists('collFlatten')) {
    function collFlatten(array $array, $depth = INF)
    {
        return collect($array)->flatten($depth);
    }
}

if (!function_exists('collTake')) {
    function collTake(array $array, int $limit)
    {
        return collect($array)->take($limit);
    }
}

if (!function_exists('arrAllValuesAreArray')) {
    function arrAllValuesAreArray(array $array)
    {
        $filtered = Arr::where($array, function ($value, $key) {
            return !is_array($value);
        });
        return (count($filtered) === 0);
    }
}

if (! function_exists('broadcastingIsActive')) {
    function broadcastingIsActive(): bool
    {
        return (bool)config('kalion.broadcasting_enabled');
    }
}

if (! function_exists('getUrlFromRoute')) {
    function getUrlFromRoute($route): string
    {
        try {
            return is_null($route) ? '#' : route($route);
        } catch (\Symfony\Component\Routing\Exception\RouteNotFoundException $exception) {
            return '#';
        }
    }
}

if (!function_exists('getIconClasses')) {
    /**
     * @deprecated This function is deprecated and will be removed in a future version. Alternatively you can use "$attributes->mergeTailwind()".
     */
    function getIconClasses(ComponentAttributeBag $attributes, $sizeNumber = '6'): string
    {
        trigger_error('The function "getIconClasses()" is deprecated.', E_USER_DEPRECATED);
        $classes = $attributes->get('class', '');
        // Si no hay un tamaño específico, se asegura de usar 'size-6' como base
        if (!str_contains($classes, 'size-')) {
            $classes = 'size-' . $sizeNumber . ' ' . $classes;
        }
        return trim($classes);
    }
}

if (!function_exists('getOtherAttributes')) {
    /**
     * @deprecated This function is deprecated and will be removed in a future version.
     */
    function getOtherAttributes(ComponentAttributeBag $attributes): ComponentAttributeBag
    {
        trigger_error('The function "getOtherAttributes()" is deprecated.', E_USER_DEPRECATED);
        return $attributes->filter(function (string $value, string $key) {
            return $key != 'class' && $key != 'outline' && $key != 'flowbite';
        });
    }
}

if (!function_exists('getIconFullAttributes')) {
    /**
     * @deprecated This function is deprecated and will be removed in a future version.
     */
    function getIconFullAttributes(ComponentAttributeBag $attributes, $sizeNumber = '6'): string
    {
        trigger_error('The function "getIconFullAttributes()" is deprecated.', E_USER_DEPRECATED);
        return trim('class="'.getIconClasses($attributes, $sizeNumber).'" '.getOtherAttributes($attributes)->toHtml());
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

if (!function_exists('getClassUserModel')) {
    function getClassUserModel(): string // |\Illuminate\Foundation\Auth\User
    {
        return config('auth.providers.users.model');
    }
}

if (!function_exists('getClassUserEntity')) {
    function getClassUserEntity(): string
    {
        return config('kalion_auth.entity_class');
    }
}

if (!function_exists('getHtmlLaravelDebugStackTrace')) {
    function getHtmlLaravelDebugStackTrace(Request $request, Throwable $exception): string
    {
        return app()->make(\Illuminate\Foundation\Exceptions\Renderer\Renderer::class)->render($request, $exception);
    }
}

if (!function_exists('appUrl')) {
    function appUrl(): string
    {
        return rtrim(config('app.url'), '/');
    }
}

if (!function_exists('defaultRoute')) {
    function defaultRoute(): string
    {
        return '/' . ltrim(config('kalion.default_route'), '/');
    }
}

if (!function_exists('defaultUrl')) {
    function defaultUrl(): string
    {
        return appUrl() . defaultRoute();
    }
}

//if (!function_exists('formatToTabulatorList')) {
//    function formatToTabulatorList(
//        Collection|ContractCollectionBase $collection,
//        string $value,
//        string $key,
//        bool $toLabel = false
//    ): Collection|ContractCollectionBase
//    {
//        if ($toLabel) {
//            return $collection->map(mapToLabelStructure($value, $key));
//        }
//        return $collection->pluck($value, $key)->prepend('', 'undefined')->prepend('', 'null')->prepend('', '');
//    }
//}
