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
        'entity' => \Src\Shared\Domain\Objects\Entities\UserEntity::class,
        'repository' => \Src\Shared\Infrastructure\Repositories\Eloquent\UserRepository::class,
    ],

];
