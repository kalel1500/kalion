<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Services\Repository;

use Thehouseofel\Kalion\Domain\Contracts\Repositories\PermissionRepositoryContract;
use Thehouseofel\Kalion\Domain\Contracts\Repositories\RoleRepositoryContract;
use Thehouseofel\Kalion\Domain\Objects\Entities\RoleEntity;
use Thehouseofel\Kalion\Domain\Objects\Entities\UserEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Domain\Services\PermissionParser;
use Thehouseofel\Kalion\Infrastructure\Services\Kalion;

final readonly class UserAccessChecker
{
    public function __construct(
        private RoleRepositoryContract       $repositoryRole,
        private PermissionRepositoryContract $repositoryPermission
    )
    {
    }

    public function can(UserEntity $user, string|array $permissions, array $params): bool
    {
        $array_permissions = PermissionParser::new()->getArrayPermissions($permissions, $params);
        return $array_permissions->contains(fn($params, $permission) => $this->userHasPermission($user, $permission, $params));
    }

    public function is(UserEntity $user, string|array $roles, array $params): bool
    {
        $array_roles = PermissionParser::new()->getArrayPermissions($roles, $params);
        return $array_roles->contains(fn($params, $role) => $this->userHasRole($user, $role, $params));
    }

    protected function userHasPermission(UserEntity $user, string $permission, array $params = []): bool
    {
        // Comprobar si el usuario tiene un rol con todos los permisos
        if ($user->all_permissions()) return true;

        // Obtener la Entidad Permission con todos los roles
        $permission = $this->repositoryPermission->findByName(ModelString::new($permission));

        // Recorrer los roles del permiso
        return $permission->roles()->contains(function (RoleEntity $role) use ($user, $permission, $params) {
            // Set user repository
            $repositoryUser = new (Kalion::getClassUserRepository($user->getGuard()));

            // Comprobar si el rol es query y lanzarla o comprobar si el usuario tiene ese rol
            return $role->is_query->isTrue()
                ? $repositoryUser->{$role->name->value()}($user, ...$params)
                : $user->roles()->contains('name', $role->name->value());
        });
    }

    protected function userHasRole(UserEntity $user, string $role, array $params = []): bool
    {
        $role = $this->repositoryRole->findByName(ModelString::new($role));
        return $user->roles()->contains(function (RoleEntity $userRole) use ($user, $role, $params) {
            // Set user repository
            $repositoryUser = new (Kalion::getClassUserRepository($user->getGuard()));

            if ($userRole->name->value() !== $role->name->value()) return false;
            if ($userRole->is_query->isTrue()) return $repositoryUser->{$role->name->value()}($user, ...$params);
            return true;
        });
    }

}
