<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Domain\Contracts;

interface AuthenticatableEntity
{
    public function can(string|array $permission, ...$params): bool;

    public function is(string|array $role, ...$params): bool;

    public function toArray($addPermissions = false, $addRoles = false): array;
}
