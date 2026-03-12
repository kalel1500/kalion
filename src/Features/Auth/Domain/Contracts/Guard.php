<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Auth\Domain\Contracts;

use Thehouseofel\Kalion\Features\Auth\Domain\Objects\DataObjects\LoginFieldDto;

interface Guard
{
    /**
     * Get the currently authenticated user.
     *
     * @return \Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AuthenticatableEntity|null
     */
    public function user();

    public function getLoginFieldData(): LoginFieldDto;

    /**
     * @return class-string
     */
    public function getClassUserModel(): string;

    /**
     * @return class-string<\Thehouseofel\Kalion\Features\Auth\Domain\Contracts\AuthenticatableEntity>
     */
    public function getClassUserEntity(): string;

    /**
     * @return class-string
     */
    public function getClassUserRepository(): string;
}
