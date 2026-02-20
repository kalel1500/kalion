<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Auth;

use Thehouseofel\Kalion\Core\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Auth\Contracts\Authentication;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Config\Kalion;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
class AuthenticationService implements Authentication
{
    private bool  $loadRoles;
    private mixed $userEntity = null;

    public function __construct()
    {
        $this->loadRoles = config('kalion.auth.load_roles');
    }

    public function user(string $guard = null)
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
