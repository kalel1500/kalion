<?php

namespace Thehouseofel\Kalion\Domain\Traits;

use Thehouseofel\Kalion\Domain\Objects\Entities\Collections\RoleCollection;
use Thehouseofel\Kalion\Domain\Objects\Entities\RoleEntity;
use Thehouseofel\Kalion\Domain\Services\Repository\UserAccessChecker;

trait EntityHasPermissions
{
    protected UserAccessChecker $accessChecker;

    protected function accessChecker(): UserAccessChecker
    {
        return $this->accessChecker ??= app(UserAccessChecker::class);
    }

    public function can(string|array $permission, ...$params): bool
    {
        return $this->accessChecker()->can($this, $permission, $params);
    }

    public function is(string|array $role, ...$params): bool
    {
        return $this->accessChecker()->is($this, $role, $params);
    }

    /*----------------------------------------------------------------------------------------------------------------*/
    /*--------------------------------------------------- Relations -------------------------------------------------*/

    public function roles(): RoleCollection
    {
        return $this->getRelation('roles');
    }

    public function setRoles(array $value): void
    {
        $this->setRelation($value, 'roles', RoleCollection::class);
    }


    /*----------------------------------------------------------------------------------------------------------------*/
    /*--------------------------------------------------- Properties -------------------------------------------------*/

    public function all_permissions(): bool
    {
        return $this->roles()->contains(fn(RoleEntity $role) => $role->all_permissions->isTrue());
    }
}
