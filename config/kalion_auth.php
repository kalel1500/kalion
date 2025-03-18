<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User clases
    |--------------------------------------------------------------------------
    |
    | In the following options you can configure the user classes:
    | "UserEntity" and "UserRepository"
    |
    */

    'user' => [
        'entity' => \Thehouseofel\Kalion\Domain\Objects\Entities\UserEntity::class,
        'repository' => \Thehouseofel\Kalion\Infrastructure\Repositories\UserRepository::class,
    ],
];
