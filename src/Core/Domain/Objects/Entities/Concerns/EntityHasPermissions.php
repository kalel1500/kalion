<?php

namespace Thehouseofel\Kalion\Core\Domain\Objects\Entities\Concerns;

use Thehouseofel\Kalion\Core\Domain\Objects\Entities\Attributes\RelationOf;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\Collections\RoleCollection;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\RoleEntity;
use Thehouseofel\Kalion\Core\Domain\Services\Auth\UserAccessChecker;

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

    #[RelationOf(RoleCollection::class)]
    public function roles(): RoleCollection
    {
        return $this->getRelation();
    }


    /*----------------------------------------------------------------------------------------------------------------*/
    /*--------------------------------------------------- Properties -------------------------------------------------*/

    public function all_permissions(): bool
    {
        return $this->roles()->contains(fn(RoleEntity $role) => $role->all_permissions->isTrue());
    }
}
