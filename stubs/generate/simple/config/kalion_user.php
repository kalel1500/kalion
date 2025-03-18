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

    'entity' => \Src\Shared\Domain\Objects\Entities\UserEntity::class,

    /*
    |--------------------------------------------------------------------------
    | Repository class
    |--------------------------------------------------------------------------
    |
    | In the following option you can configure the user Repository class.
    |
    */

    'repository' => \Src\Shared\Infrastructure\Repositories\Eloquent\UserRepository::class,
];
