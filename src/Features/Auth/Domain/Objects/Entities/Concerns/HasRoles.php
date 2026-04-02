<?php

namespace Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\Concerns;

use Thehouseofel\Kalion\Core\Domain\Exceptions\NeverCalledException;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\Attributes\Computed;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\Attributes\RelationOf;
use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AbilityEntity;
use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Repositories\PermissionRepository;
use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Repositories\RoleRepository;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\Collections\PermissionCollection;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\Collections\RoleCollection;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\RoleEntity;
use Thehouseofel\Kalion\Features\Auth\Domain\Support\AbilityParser;

trait HasRoles
{
    protected array $is  = [];
    protected array $can = [];

    public function can(string|array $permission, ...$params): bool
    {
        return $this->check('permissions', $permission, $params);
    }

    public function is(string|array $role, ...$params): bool
    {
        return $this->check('roles', $role, $params);
    }

    protected function check(string $method, string|array $value, array $params): bool
    {
        $values = (new AbilityParser)->parse($value, $params);
        return $values->contains(fn($params, $value) => $this->userHas($method, $value, $params));
    }

    protected function userHas(string $item, string $value, array $params = []): bool
    {
        if (! in_array($item, ['permissions', 'roles'])) {
            throw new NeverCalledException(sprintf('The method %s is not meant to be called with the item "%s".', __METHOD__, $item));
        }

        return $this->$item()->contains(function (AbilityEntity $item) use ($value, $params) {
            $repositoryUser = new (kauth($this->getGuard())->getClassUserRepository());

            if ($item->name->value !== $value) return false;
            if ($item->getIsQuery()) return $repositoryUser->{$value}($this, ...$params);
            return true;
        });
    }

    public function toArray($addPermissions = false, $addRoles = false): array
    {
        if ($addPermissions) {
            $repositoryPermission = app(PermissionRepository::class);
            foreach ($repositoryPermission->all() as $permission) {
                $permissionName = $permission->name->value;
                $this->can[$permissionName] = $this->can($permissionName);
            }
        }

        if ($addRoles) {
            $repositoryRole = app(RoleRepository::class);
            foreach ($repositoryRole->all() as $role) {
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
