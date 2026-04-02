<?php

namespace Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\Concerns;

use Thehouseofel\Kalion\Core\Domain\Objects\Entities\Attributes\Computed;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\Attributes\RelationOf;
use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Repositories\PermissionRepository;
use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Repositories\RoleRepository;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\Collections\PermissionCollection;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\Collections\RoleCollection;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\RoleEntity;
use Thehouseofel\Kalion\Features\Auth\Domain\Support\UserAccessChecker;

trait HasRoles
{
    protected UserAccessChecker $accessChecker;
    protected RoleRepository $repositoryRole;
    protected PermissionRepository $repositoryPermission;

    protected array $is  = [];
    protected array $can = [];

    protected function accessChecker(): UserAccessChecker
    {
        return $this->accessChecker ??= app(UserAccessChecker::class);
    }

    protected function repositoryRole(): RoleRepository
    {
        return $this->repositoryRole ??= app(RoleRepository::class);
    }

    protected function repositoryPermission(): PermissionRepository
    {
        return $this->repositoryPermission ??= app(PermissionRepository::class);
    }

    public function can(string|array $permission, ...$params): bool
    {
        return $this->accessChecker()->can($this, $permission, $params);
    }

    public function is(string|array $role, ...$params): bool
    {
        return $this->accessChecker()->is($this, $role, $params);
    }

    public function toArray($addPermissions = false, $addRoles = false): array
    {
        if ($addPermissions) {
            foreach ($this->repositoryPermission()->all() as $permission) {
                $permissionName = $permission->name->value;
                $this->can[$permissionName] = $this->can($permissionName);
            }
        }

        if ($addRoles) {
            foreach ($this->repositoryRole()->all() as $role) {
                $roleName = $role->name->value;
                $this->is[$roleName] = $this->is($roleName);
            }
        }

        $base = parent::toArray();
        $base['is'] = $this->is;
        $base['can'] = $this->can;
        return $base;
    }

    /*----------------------------------------------------------------------------------------------------------------*/
    /*--------------------------------------------------- Relations -------------------------------------------------*/

    #[RelationOf(RoleCollection::class)]
    public function roles(): RoleCollection
    {
        return $this->getRelation();
    }

    #[RelationOf(PermissionCollection::class)]
    public function permissions(): PermissionCollection
    {
        return $this->getRelation();
    }

    /*----------------------------------------------------------------------------------------------------------------*/
    /*--------------------------------------------------- Properties -------------------------------------------------*/

    #[Computed(Computed::AS_ATTRIBUTE)]
    public function all_permissions(): bool
    {
        return $this->computed(fn() => $this->roles()->contains(fn(RoleEntity $role) => $role->all_permissions->isTrue()));
    }
}
