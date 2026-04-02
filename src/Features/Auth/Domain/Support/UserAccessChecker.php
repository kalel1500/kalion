<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Domain\Support;

use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\PermissionEntity;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\RoleEntity;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\UserEntity;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
final readonly class UserAccessChecker
{
    public function __construct(
        private PermissionParser     $permissionParser
    )
    {
    }

    public function can(UserEntity $user, string|array $permissions, array $params): bool
    {
        $array_permissions = $this->permissionParser->getArrayPermissions($permissions, $params);
        return $array_permissions->contains(fn($params, $permission) => $this->userHasPermission($user, $permission, $params));
    }

    public function is(UserEntity $user, string|array $roles, array $params): bool
    {
        $array_roles = $this->permissionParser->getArrayPermissions($roles, $params);
        return $array_roles->contains(fn($params, $role) => $this->userHasRole($user, $role, $params));
    }

    protected function userHasPermission(UserEntity $user, string $permission, array $params = []): bool
    {
        return $user->permissions()->contains(function (PermissionEntity $userPermission) use ($user, $permission, $params) {
            $repositoryUser = new (kauth($user->getGuard())->getClassUserRepository());

            if ($userPermission->name->value !== $permission) return false;
            if ($userPermission->getIsQuery()) return $repositoryUser->{$permission}($user, ...$params);
            return true;
        });
    }

    protected function userHasRole(UserEntity $user, string $role, array $params = []): bool
    {
        return $user->roles()->contains(function (RoleEntity $userRole) use ($user, $role, $params) {
            $repositoryUser = new (kauth($user->getGuard())->getClassUserRepository());

            if ($userRole->name->value !== $role) return false;
            if ($userRole->getIsQuery()) return $repositoryUser->{$role}($user, ...$params);
            return true;
        });
    }

}
