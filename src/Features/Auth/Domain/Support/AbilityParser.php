<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Domain\Support;

use Illuminate\Support\Collection;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
class AbilityParser
{
    public function parse(string|array $abilities, array $params): Collection
    {
        $abilities = collect(is_array($abilities) ? $abilities : explode('|', $abilities));

        return empty($params)
            ? $this->parseFromStrings($abilities)
            : $this->parseFromArrays($abilities, $params);
    }

    private function parseFromStrings(Collection $abilities): Collection
    {
        return $abilities->mapWithKeys(function ($permission) {
            [$permission_name, $permission_params] = array_pad(explode(':', $permission, 2), 2, null);

            return [$permission_name => $this->parseParamsFromString($permission_params)];
        });
    }

    private function parseParamsFromString(?string $params): array
    {
        if (is_null($params)) {
            return [];
        }

        return collect(explode(';', $params))
            ->map(fn($param) => $this->normalizeParamValues(explode(',', $param)))
            ->toArray();
    }

    private function normalizeParamValues(array $paramValues)
    {
        return count($paramValues) === 1
            ? (is_numeric($paramValues[0]) ? intval($paramValues[0]) : $paramValues[0])
            : array_map(fn($val) => is_numeric($val) ? intval($val) : $val, $paramValues);
    }

    private function parseFromArrays(Collection $permissions, array $params): Collection
    {
        return $permissions->mapWithKeys(function ($permission, $key) use ($params) {
            return [$permission => $this->normalizeArrayParams($params[$key] ?? null)];
        });
    }

    private function normalizeArrayParams($param): array
    {
        if (is_null($param) || (is_array($param) && empty(array_filter($param, fn($p) => ! is_null($p))))) {
            return [];
        }

        return is_array($param)
            ? (is_array($param[0]) ? $param : [$param])
            : [$param];
    }

}
