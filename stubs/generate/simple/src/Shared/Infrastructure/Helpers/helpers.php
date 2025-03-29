<?php

declare(strict_types=1);

use Src\Shared\Domain\Objects\Entities\UserEntity;
use Thehouseofel\Kalion\Domain\Objects\Entities\ApiUserEntity;
use Thehouseofel\Kalion\Infrastructure\Facades\AuthService;

if (!function_exists('userEntity')) {
    // Este helper se crea en la aplicación para indicar el return es de tipo UserEntity
    /**
     * Get the currently authenticated user entity.
     *
     * @return UserEntity|ApiUserEntity|null
     */
    function userEntity()
    {
        return AuthService::userEntity();
    }
}
