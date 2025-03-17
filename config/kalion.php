<?php

use Illuminate\Support\Str;

return [

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
    | packages that you want to be able to start with the command job:dispatch {job}.
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
