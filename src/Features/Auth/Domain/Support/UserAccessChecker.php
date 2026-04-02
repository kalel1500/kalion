<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Domain\Support;

use Thehouseofel\Kalion\Core\Domain\Exceptions\NeverCalledException;
use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AccessEntity;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\UserEntity;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
final readonly class UserAccessChecker
{
    public function __construct(
        private PermissionParser $permissionParser
    )
    {
    }

    public function can(UserEntity $user, string|array $permissions, array $params): bool
    {
        return $this->check('can', $user, $permissions, $params);
    }

    public function is(UserEntity $user, string|array $roles, array $params): bool
    {
        return $this->check('is', $user, $roles, $params);
    }

    protected function check(string $method, UserEntity $user, string|array $value, array $params): bool
    {
        $values = $this->permissionParser->getArrayPermissions($value, $params);
        return $values->contains(fn($params, $value) => $this->userHas($method, $user, $value, $params));
    }

    protected function userHas(string $item, UserEntity $user, string $value, array $params = []): bool
    {
        if (! in_array($item, ['permissions', 'roles'])) {
            throw new NeverCalledException(sprintf('The method %s is not meant to be called with the item "%s".', __METHOD__, $item));
        }

        return $user->$item()->contains(function (AccessEntity $item) use ($user, $value, $params) {
            $repositoryUser = new (kauth($user->getGuard())->getClassUserRepository());

            if ($item->name->value !== $value) return false;
            if ($item->getIsQuery()) return $repositoryUser->{$value}($user, ...$params);
            return true;
        });
    }
}
