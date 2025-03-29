<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Entity class
    |--------------------------------------------------------------------------
    |
    | In the following option you can configure the user Entity class.
    |
    */

    'entities' => [
        'web' => \Src\Shared\Domain\Objects\Entities\UserEntity::class,
        'api' => \Thehouseofel\Kalion\Domain\Objects\Entities\ApiUserEntity::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Repository class
    |--------------------------------------------------------------------------
    |
    | In the following option you can configure the user Repository class.
    |
    */

    'repositories' => [
        'web' => \Src\Shared\Infrastructure\Repositories\Eloquent\UserRepository::class,
        'api' => \Thehouseofel\Kalion\Infrastructure\Repositories\ApiUserRepository::class,
    ],
];
