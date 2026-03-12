<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Infrastructure\Support;

use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Guard;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\DataObjects\LoginFieldDto;

class EntityGuard implements Guard
{
    protected mixed $userEntity = null;

    public function __construct(
        protected string $guard
    )
    {
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AuthenticatableEntity|null
     */
    public function user()
    {
        if ($this->userEntity) {
            return $this->userEntity;
        }

        $user = auth($this->guard)->user();

        if (! $user) {
            return null;
        }

        $with = null;
        if (config('kalion.auth.load_roles')) {
            $user->load('roles');
            $with = ['roles'];
        }

        $this->userEntity = $this->getClassUserEntity()::fromArray($user->toArray(), $with);
        $this->userEntity->setGuard($this->guard);

        return $this->userEntity;
    }


    public function getLoginFieldData(): LoginFieldDto
    {
        $defaultField = config('kalion.auth.fields.' . $this->guard);
        $fields       = config('kalion.auth.available_fields');
        $field        = $fields[$defaultField] ?? $fields['email'];
        return new LoginFieldDto(
            name       : $field['name'],
            label      : $field['label'],
            type       : $field['type'],
            placeholder: $field['placeholder'],
        );
    }

    /**
     * @return class-string
     */
    public function getClassUserModel(): string // |\Illuminate\Foundation\Auth\User
    {
        $provider = config('auth.guards.' . $this->guard . '.provider');
        return config('auth.providers.' . $provider . '.model');
    }

    /**
     * @return class-string<\Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AuthenticatableEntity>
     */
    public function getClassUserEntity(): string
    {
        return config('kalion.auth.entities.' . $this->guard);
    }

    /**
     * @return class-string
     */
    public function getClassUserRepository(): string
    {
        return config('kalion.auth.repositories.' . $this->guard);
    }
}
