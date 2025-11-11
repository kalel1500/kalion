<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Services\Auth;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;
use Thehouseofel\Kalion\Core\Infrastructure\Services\Kalion;
use Thehouseofel\Kalion\Features\Shared\Domain\Contracts\Repositories\PermissionRepository;
use Thehouseofel\Kalion\Features\Shared\Domain\Contracts\Repositories\RoleRepository;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\RoleEntity;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\UserEntity;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
final readonly class UserAccessChecker
{
    public function __construct(
        private RoleRepository       $repositoryRole,
        private PermissionRepository $repositoryPermission
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
        $permission = $this->repositoryPermission->findByName(StringVo::from($permission));

        // Recorrer los roles del permiso
        return $permission->roles()->contains(function (RoleEntity $role) use ($user, $permission, $params) {
            // Set user repository
            $repositoryUser = new (Kalion::getClassUserRepository($user->getGuard()));

            // Comprobar si el rol es query y lanzarla o comprobar si el usuario tiene ese rol
            return $role->is_query->isTrue()
                ? $repositoryUser->{$role->name->value}($user, ...$params)
                : $user->roles()->contains('name', $role->name->value);
        });
    }

    protected function userHasRole(UserEntity $user, string $role, array $params = []): bool
    {
        $role = $this->repositoryRole->findByName(StringVo::from($role));
        return $user->roles()->contains(function (RoleEntity $userRole) use ($user, $role, $params) {
            // Set user repository
            $repositoryUser = new (Kalion::getClassUserRepository($user->getGuard()));

            if ($userRole->name->value !== $role->name->value) return false;
            if ($userRole->is_query->isTrue()) return $repositoryUser->{$role->name->value}($user, ...$params);
            return true;
        });
    }

}
