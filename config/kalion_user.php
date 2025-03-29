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
        'web' => env('KALION_USER_ENTITY_WEB', \Thehouseofel\Kalion\Domain\Objects\Entities\UserEntity::class),
        'api' => env('KALION_USER_ENTITY_API', \Thehouseofel\Kalion\Domain\Objects\Entities\ApiUserEntity::class),
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
        'web' => env('KALION_USER_REPOSITORY_WEB', \Thehouseofel\Kalion\Infrastructure\Repositories\UserRepository::class),
        'api' => env('KALION_USER_REPOSITORY_API', \Thehouseofel\Kalion\Infrastructure\Repositories\ApiUserRepository::class),
    ],
];
