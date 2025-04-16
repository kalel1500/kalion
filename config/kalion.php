<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | ServiceProvider Configurations
    |--------------------------------------------------------------------------
    |
    | In the following options you can configure what will be registered
    | in the ServiceProvider
    |
    | - "run_migrations": Sets whether migrations should be run with
    |                     the "php artisan migrate" command.
    |
    | - "publish_migrations": Sets whether migrations should be published
    |                         with the "php artisan vendor:publish" command.
    |
    | - "register_routes": Sets whether package routes should be registered.
    |
    | - "enable_preferences_cookie": Sets whether the AddPreferencesCookies
    |                                middleware should be added to the web routes group.
    |
    */

    'run_migrations' => (bool) env('KALION_RUN_MIGRATIONS', false),

    'publish_migrations' => (bool) env('KALION_PUBLISH_MIGRATIONS', false),

    'register_routes' => (bool) env('KALION_REGISTER_ROUTES', true),

    'enable_preferences_cookie' => (bool) env('KALION_ENABLE_PREFERENCES_COOKIE', true),

    /*
    |--------------------------------------------------------------------------
    | Default Route
    |--------------------------------------------------------------------------
    |
    | The following options allow you to configure the default route to which
    | the application should redirect you
    |
    */

    'default_route' => env('KALION_DEFAULT_ROUTE', '/home'),

    'default_route_name' => env('KALION_DEFAULT_ROUTE_NAME', 'home'),

    /*
    |--------------------------------------------------------------------------
    | Real environment during testing
    |--------------------------------------------------------------------------
    |
    | It is equivalent to the 'app.env' that you are in when doing the tests,
    | since during the tests the value of 'app.env' testing.
    |
    */

    'real_env_in_tests' => env('KALION_REAL_ENV_IN_TESTS', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | The following options are used to configure the emails. If the sending
    | of emails is active, if the tests and the test recipients are active.
    |
    */

    'mail_is_active' => (bool) env('KALION_MAIL_IS_ACTIVE', false),

    'mail_active_tests' => (bool) env('KALION_MAIL_ACTIVE_TESTS', false),

    'mail_test_recipients' => env('KALION_MAIL_TEST_RECIPIENTS'),

    /*
    |--------------------------------------------------------------------------
    | Broadcasting
    |--------------------------------------------------------------------------
    |
    | With this option you can activate or deactivate broadcasting.
    |
    */

    'broadcasting_enabled' => (bool) env('KALION_BROADCASTING_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Calculated properties of entities
    |--------------------------------------------------------------------------
    |
    | With this option you can configure how entity relationships should behave
    | by default. When you instantiate an entity with relationships, each entity
    | can be generated in a simple way with only its basic properties or in a
    | complete way by adding the calculated properties. This can be indicated every
    | time an entity is created as a third parameter and also in the relationships
    | with the flag 's' (simple) or 'f' (full).
    |
    | With this option you can indicate how entities should behave by default.
    |
    | Supported values: "f", "s"
    |
    */

    'entity_calculated_props_mode' => env('KALION_ENTITY_CALCULATED_PROPS_MODE', 's'),

    /*
    |--------------------------------------------------------------------------
    | ModelId value object
    |--------------------------------------------------------------------------
    |
    | The following option allows you to configure the minimum value allowed
    | in the Value Object "ModelId"
    |
    */

    'minimum_value_for_model_id' => (int) env('KALION_MINIMUM_VALUE_FOR_MODEL_ID', 1),

    /*
    |--------------------------------------------------------------------------
    | Jobs
    |--------------------------------------------------------------------------
    |
    | In the next option you can define the namespaces of the jobs of other
    | packages that you want to be able to start with the command kalion:job-dispatch {job}.
    |
    | You can define an array of strings or a string with several packages
    | separated by ";"
    |
    */

    'packages_to_scan_for_jobs' => env('KALION_PACKAGES_TO_SCAN_FOR_JOBS'),

    /*
    |--------------------------------------------------------------------------
    | Cookies
    |--------------------------------------------------------------------------
    |
    | The next option allows you to set the cookie package prefix and duration.
    |
    */

    'cookie' => [
        'name' => Str::slug(env('APP_NAME', 'laravel'), '_').'_kalion_user_preferences',
        'duration' => (int) env('KALION_COOKIE_DURATION', (60 * 24 * 364)),
        'version' => env('KALION_COOKIE_VERSION', "0"),
    ],

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | In the following options you can configure the layout options.
    |
    */

    'layout' => [
        'theme' => env('KALION_LAYOUT_THEME'),
        'active_shadows' => (bool) env('KALION_LAYOUT_ACTIVE_SHADOWS', false),
        'sidebar_collapsed' => (bool) env('KALION_LAYOUT_SIDEBAR_COLLAPSED', false),
        'sidebar_state_per_page' => (bool) env('KALION_LAYOUT_SIDEBAR_STATE_PER_PAGE', false),
        'blade_show_main_border' => (bool) env('KALION_LAYOUT_BLADE_SHOW_MAIN_BORDER', false),
    ],


    /*
    |--------------------------------------------------------------------------
    | Authentication Settings
    |--------------------------------------------------------------------------
    |
    | These configuration options control the authentication behavior of Kalion.
    |
    | - "fake": Enables fake login mode, bypassing password authentication.
    |           Should only be used in local environments for testing.
    | - "fields": Defines which database field will be used for authentication.
    | - "available_fields": Contains all available login fields and their attributes.
    | - "load_roles": If enabled, user roles will be loaded automatically.
    | - "display_role_in_exception": Shows required roles in exception messages.
    | - "display_permission_in_exception": Shows required permissions in exceptions.
    |
    */

    'auth' => [
        'fake' => (bool) env('KALION_AUTH_FAKE', false),
        'blades' => [
            'fake' => env('KALION_AUTH_BLADE_FAKE', 'kal::pages.auth.fake'),
            'login' => env('KALION_AUTH_BLADE_LOGIN', 'kal::pages.auth.login'),
            'register' => env('KALION_AUTH_BLADE_REGISTER', 'kal::pages.auth.register'),
        ],
        'entities' => [
            'web' => env('KALION_AUTH_ENTITY_WEB', Thehouseofel\Kalion\Domain\Objects\Entities\UserEntity::class),
            'api' => env('KALION_AUTH_ENTITY_API', Thehouseofel\Kalion\Domain\Objects\Entities\ApiUserEntity::class),
        ],
        'repositories' => [
            'web' => env('KALION_AUTH_REPOSITORY_WEB', Thehouseofel\Kalion\Infrastructure\Repositories\UserRepository::class),
            'api' => env('KALION_AUTH_REPOSITORY_API', Thehouseofel\Kalion\Infrastructure\Repositories\ApiUserRepository::class),
        ],
        'fields' => [
            'web' => env('KALION_AUTH_FIELD', 'email'),
            'api' => env('KALION_AUTH_FIELD_API', 'name'),
        ],
        'available_fields' => [
            'id' => [
                'name' => 'id',
                'label' => 'Id',
                'type' => 'number',
                'placeholder' => 'ID',
            ],
            'email' => [
                'name' => 'email',
                'label' => 'Email',
                'type' => 'email',
                'placeholder' => 'you@somewhere.com',
            ],
            'matricula' => [
                'name' => 'matricula',
                'label' => 'Matricula',
                'type' => 'text',
                'placeholder' => 'Matricula',
            ],
            'custom' => [
                'name' => env('KALION_AUTH_FIELD_NAME', 'email'),
                'label' => env('KALION_AUTH_FIELD_LABEL', 'Email'),
                'type' => env('KALION_AUTH_FIELD_TYPE', 'email'),
                'placeholder' => env('KALION_AUTH_FIELD_PLACEHOLDER', 'you@somewhere.com'),
            ]
        ],
        'load_roles' => (bool) env('KALION_AUTH_LOAD_ROLES', true),
        'display_role_in_exception' => (bool) env('KALION_AUTH_DISPLAY_ROLE_IN_EXCEPTION', false),
        'display_permission_in_exception' => (bool) env('KALION_AUTH_DISPLAY_PERMISSION_IN_EXCEPTION', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | NPM engines versions for the Start command
    |--------------------------------------------------------------------------
    |
    | With these options you can configure which versions of npm engines will
    | be added to "package.json"
    |
    */

    'version_node' => env('KALION_VERSION_NODE', '^20.11.1'),

    'version_npm' => env('KALION_VERSION_NPM', '^10.5.0'),

    /*
    |--------------------------------------------------------------------------
    | Package in develop
    |--------------------------------------------------------------------------
    |
    | With this option you can configure if the package is in development to
    | avoid executing unnecessary methods in the "kalion:start" command.
    |
    */

    'package_in_develop' => (bool) env('KALION_PACKAGE_IN_DEVELOP', false),
    'keep_migrations_date' => (bool) env('KALION_KEEP_MIGRATIONS_DATE', false),
];
