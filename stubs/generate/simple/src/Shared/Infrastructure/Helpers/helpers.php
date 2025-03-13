<?php

declare(strict_types=1);

use Src\Shared\Domain\Objects\Entities\UserEntity;
use Thehouseofel\Kalion\Infrastructure\Facades\AuthService;

if (!function_exists('userEntity')) {
    /**
     * Este helper se crea en la aplicación (y no en el paquete) para indicar el return es de tipo UserEntity
     */
    function userEntity(): ?UserEntity
    {
        /** @var UserEntity $user */
        $user = AuthService::userEntity();
        return $user;
    }
}
