<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services\Auth;

use Thehouseofel\Kalion\Domain\Contracts\Services\Auth\CurrentUser;
use Thehouseofel\Kalion\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Infrastructure\Services\Kalion;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
class CurrentUserService implements CurrentUser
{
    private bool    $loadRoles;
    private mixed   $userEntity = null;

    public function __construct()
    {
        $this->loadRoles = config('kalion.auth.load_roles');
    }

    public function userEntity(string $guard = null)
    {
        /** @var class-string<AbstractEntity> $entityClass */
        $entityClass = Kalion::getClassUserEntity($guard);

        if ($this->userEntity && $this->userEntity->getGuard() === $guard) {
            return $this->userEntity;
        }

        $user = auth($guard)->user();
        if (is_null($user)) {
            return null;
        }

        $with = null;
        if ($this->loadRoles) {
            $user->load('roles');
            $with = ['roles'];
        }
        $this->userEntity = $entityClass::fromArray($user->toArray(), $with);
        $this->userEntity->setGuard($guard);
        return $this->userEntity;
    }
}
