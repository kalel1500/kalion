<?php

declare(strict_types=1);

namespace Thehouseofel\Hexagonal\Domain\Services;

use Illuminate\Support\Collection;

final class PermissionParser
{
    public static function new(): self
    {
        return new self();
    }

    /**
     * @param string|array $permissions
     * @param array $params
     * @return Collection
     */
    public function getArrayPermissions($permissions, array $params): Collection
    {
        $permissions = collect(is_array($permissions) ? $permissions : explode('|', $permissions));

        return empty($params)
            ? $this->parseStringPermissions($permissions)
            : $this->parseArrayPermissions($permissions, $params);
    }

    private function parseStringPermissions(Collection $permissions): Collection
    {
        return $permissions->mapWithKeys(function ($permission) {
            [$permission_name, $permission_params] = array_pad(explode(':', $permission, 2), 2, null);

            return [$permission_name => $this->parseParamsFromString($permission_params)];
        });
    }

    private function parseArrayPermissions(Collection $permissions, array $params): Collection
    {
        return $permissions->mapWithKeys(function ($permission, $key) use ($params) {
            return [$permission => $this->normalizeArrayParams($params[$key] ?? null)];
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

    private function normalizeArrayParams($param): array
    {
        if (is_null($param) || (is_array($param) && empty(array_filter($param, fn($p) => !is_null($p))))) {
            return [];
        }

        return is_array($param)
            ? (is_array($param[0]) ? $param : [$param])
            : [$param];
    }

    private function normalizeParamValues(array $paramValues)
    {
        return count($paramValues) === 1
            ? (is_numeric($paramValues[0]) ? intval($paramValues[0]) : $paramValues[0])
            : array_map(fn($val) => is_numeric($val) ? intval($val) : $val, $paramValues);
    }
}