<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services\Auth;

use Thehouseofel\Kalion\Domain\Contracts\Services\CurrentUserContract;
use Thehouseofel\Kalion\Domain\Objects\Entities\UserEntity;
use Thehouseofel\Kalion\Infrastructure\Services\Kalion;

/**
 * @template T of UserEntity
 */
final class CurrentUser implements CurrentUserContract
{
    private bool $loadRoles;
    private string  $entityClass;
    private ?string $guard;

    /** @var UserEntity|null */
    private $userEntity = null;

    public function __construct()
    {
        $this->loadRoles = config('kalion.auth.load_roles');
    }

    /**
     * @param string|null $guard
     * @return UserEntity|null
     */
    public function userEntity(string $guard = null)
    {
        $this->guard = $guard;
        $this->entityClass = Kalion::getClassUserEntity($this->guard);

        if ($this->userEntity && $this->userEntity->getGuard() === $this->guard) {
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
        $this->userEntity =  $this->entityClass::fromArray($user->toArray(), $with);
        $this->userEntity->setGuard($guard);
        return $this->userEntity;
    }
}
