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

    /*
    |--------------------------------------------------------------------------
    | User clases
    |--------------------------------------------------------------------------
    |
    | These configuration options specify the behavior of Kalion's login
    | functionality.
    |
    | The "fake" property allows enabling fake login. This feature should
    | only be used in a local environment.
    |
    */

    'login' => [
        'fake' => (bool) env('KALION_AUTH_LOGIN_FAKE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Load user roles
    |--------------------------------------------------------------------------
    |
    | With the following option you can configure whether the roles of
    | the authenticated user are automatically loaded
    |
    */

    'load_roles' => (bool) env('KALION_AUTH_LOAD_ROLES', true),

    /*
    |--------------------------------------------------------------------------
    | Display role in exception
    |--------------------------------------------------------------------------
    |
    | When set to true, the required role names are added to exception messages.
    | This could be considered an information leak in some contexts, so the default
    | setting is false here for optimum safety.
    |
    */

    'display_role_in_exception' => (bool) env('KALION_AUTH_DISPLAY_ROLE_IN_EXCEPTION', false),

    /*
    |--------------------------------------------------------------------------
    | Display role in exception
    |--------------------------------------------------------------------------
    |
    | When set to true, the required permission names are added to exception messages.
    | This could be considered an information leak in some contexts, so the default
    | setting is false here for optimum safety.
    |
    */

    'display_permission_in_exception' => (bool) env('KALION_AUTH_DISPLAY_PERMISSION_IN_EXCEPTION', false),
];
