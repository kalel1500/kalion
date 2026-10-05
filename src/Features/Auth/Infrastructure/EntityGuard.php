<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Infrastructure;

use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AuthenticatableEntity;
use Thehouseofel\Kalion\Features\Auth\Domain\Contracts\Guard;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\DataObjects\LoginFieldDto;

class EntityGuard implements Guard
{
    protected ?AuthenticatableEntity $userEntity = null;

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
        if (kalion()->config()->abilitiesEnabled()) {
            $with = ['roles', 'permissions.roles'];
            $user->load($with);
        }

        $this->userEntity = $this->getClassUserEntity()::fromArray($user->toArray(), $with);
        $this->userEntity->setGuard($this->guard);
        $this->userEntity->fillStaticAbilities();

        return $this->userEntity;
    }


    public function getLoginFieldData(): LoginFieldDto
    {
        $defaultField = $this->providerConfig('field');
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
     * Kalion config is the source of truth (it is synced into Laravel's auth config by the KalionServiceProvider).
     * Laravel's config is only used as a fallback for providers not managed by Kalion.
     *
     * @return class-string
     */
    public function getClassUserModel(): string // |\Illuminate\Foundation\Auth\User
    {
        return $this->providerConfig('model') ?? config('auth.providers.' . $this->getProviderName() . '.model');
    }

    /**
     * @return class-string<\Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AuthenticatableEntity>
     */
    public function getClassUserEntity(): string
    {
        return $this->providerConfig('entity');
    }

    /**
     * @return class-string|null
     */
    public function getClassAbilityRepository(): ?string
    {
        return $this->providerConfig('ability_repository');
    }

    /**
     * Get the name of the Laravel auth provider used by this guard.
     */
    protected function getProviderName(): ?string
    {
        return config('auth.guards.' . $this->guard . '.provider');
    }

    /**
     * Get a value from the Kalion configuration of the provider used by this guard.
     */
    protected function providerConfig(string $key, mixed $default = null): mixed
    {
        $provider = $this->getProviderName();

        if ($provider === null) {
            return $default;
        }

        return config('kalion.auth.providers.' . $provider . '.' . $key, $default);
    }
}
