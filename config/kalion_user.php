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

    'entity' => \Thehouseofel\Kalion\Domain\Objects\Entities\UserEntity::class,

    /*
    |--------------------------------------------------------------------------
    | Repository class
    |--------------------------------------------------------------------------
    |
    | In the following option you can configure the user Repository class.
    |
    */

    'repository' => \Thehouseofel\Kalion\Infrastructure\Repositories\UserRepository::class,
];
