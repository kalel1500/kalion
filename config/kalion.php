<?php

use Illuminate\Support\Str;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\SidebarState;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Config\KalionConfig;

$defaults = KalionConfig::getDefaults();

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
    | - "register_routes": Sets whether package routes should be registered.
    |
    | - "web_middlewares": Sets whether the following middlewares should be
    |                      added to the web route group: [AddPreferencesCookies, ForceArraySessionInCloud]
    |
    */

    'run_migrations' => (bool) env('KALION_RUN_MIGRATIONS', $defaults['kalion.run_migrations']),

    'register_routes' => (bool) env('KALION_REGISTER_ROUTES', $defaults['kalion.register_routes']),

    'web_middlewares' => [
        'add_preferences_cookies' => [
            'active' => (bool) env('KALION_WEB_MIDDLEWARE_ADD_PREFERENCES_COOKIES_ACTIVE', $defaults['kalion.web_middlewares.add_preferences_cookies.active']),
        ],

        'force_array_session_in_cloud' => [
            'active' => (bool) env('KALION_WEB_MIDDLEWARE_FORCE_ARRAY_SESSION_IN_CLOUD_ACTIVE', $defaults['kalion.web_middlewares.force_array_session_in_cloud.active']),
            'cloud_user_agent_value' => env('KALION_WEB_MIDDLEWARE_FORCE_ARRAY_SESSION_IN_CLOUD_CLOUD_USER_AGENT_VALUE', $defaults['kalion.web_middlewares.force_array_session_in_cloud.cloud_user_agent_value']),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Path
    |--------------------------------------------------------------------------
    |
    | The following option allow you to configure the default path to which
    | the application should redirect you
    |
    */

    'default_path' => env('KALION_DEFAULT_PATH', $defaults['kalion.default_path']),

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

    'broadcasting_enabled' => (bool) env('KALION_BROADCASTING_ENABLED', $defaults['kalion.broadcasting_enabled']),

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

    'entity_calculated_props_mode' => env('KALION_ENTITY_CALCULATED_PROPS_MODE', $defaults['kalion.entity_calculated_props_mode']),

    /*
    |--------------------------------------------------------------------------
    | Id value object
    |--------------------------------------------------------------------------
    |
    | The following option allows you to configure the minimum value allowed
    | in the Value Object "IdVo"
    |
    */

    'minimum_value_for_id' => (int) env('KALION_MINIMUM_VALUE_FOR_ID', $defaults['kalion.minimum_value_for_id']),

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

    'packages_to_scan_for_jobs' => env('KALION_PACKAGES_TO_SCAN_FOR_JOBS', ''),

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
        'duration' => (int) env('KALION_COOKIE_DURATION', $defaults['kalion.cookie.duration']),
        'version' => env('KALION_COOKIE_VERSION', $defaults['kalion.cookie.version']),
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
        'default_theme' => env('KALION_LAYOUT_DEFAULT_THEME', $defaults['kalion.layout.default_theme']),

        'use_elevated_shadows' => (bool) env('KALION_LAYOUT_USE_ELEVATED_SHADOWS', $defaults['kalion.layout.use_elevated_shadows']), // por si crece: 'shadow_style' => 'default', // default | elevated | none

        'navbar_density' => env('KALION_LAYOUT_NAVBAR_DENSITY', $defaults['kalion.layout.navbar_density']), // tight | compact | normal | comfortable

        'default_sidebar_state' => env('KALION_LAYOUT_DEFAULT_SIDEBAR_STATE', $defaults['kalion.layout.default_sidebar_state']),  // expanded | collapsed

        'sidebar_state_per_page' => (bool) env('KALION_LAYOUT_SIDEBAR_STATE_PER_PAGE', $defaults['kalion.layout.sidebar_state_per_page']),

        'sidebar_disabled' => (bool) env('KALION_LAYOUT_SIDEBAR_DISABLED', $defaults['kalion.layout.sidebar_disabled']),

        'show_footer' => (bool) env('KALION_LAYOUT_SHOW_FOOTER', $defaults['kalion.layout.show_footer']),

        'show_debug_main_border' => (bool) env('KALION_LAYOUT_SHOW_DEBUG_MAIN_BORDER', $defaults['kalion.layout.show_debug_main_border']),

        'data_provider' => env('KALION_LAYOUT_DATA_PROVIDER', $defaults['kalion.layout.data_provider']),

        'logo_path' => env('KALION_LAYOUT_LOGO_PATH', $defaults['kalion.layout.logo_path']),

        'favicon_path' => env('KALION_LAYOUT_FAVICON_PATH', $defaults['kalion.layout.favicon_path']),
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
        'fake' => (bool) env('KALION_AUTH_FAKE', $defaults['kalion.auth.fake']),
        'disable_register' => (bool) env('KALION_AUTH_DISABLE_REGISTER', $defaults['kalion.auth.disable_register']),
        'disable_password_reset' => (bool) env('KALION_AUTH_DISABLE_PASSWORD_RESET', $defaults['kalion.auth.disable_password_reset']),
        'redirect_after_login' => env('KALION_AUTH_REDIRECT_AFTER_LOGIN', $defaults['kalion.auth.redirect_after_login']),
        'blades' => [
            'fake' => env('KALION_AUTH_BLADE_FAKE', $defaults['kalion.auth.blades.fake']),
            'login' => env('KALION_AUTH_BLADE_LOGIN', $defaults['kalion.auth.blades.login']),
            'register' => env('KALION_AUTH_BLADE_REGISTER', $defaults['kalion.auth.blades.register']),
            'password_reset' => env('KALION_AUTH_BLADE_PASSWORD_RESET', $defaults['kalion.auth.blades.password_reset']),
        ],
        'models' => [
            'web' => env('KALION_AUTH_MODEL_WEB', $defaults['kalion.auth.models.web']),
            'api' => env('KALION_AUTH_MODEL_API', $defaults['kalion.auth.models.api']),
        ],
        'entities' => [
            'web' => env('KALION_AUTH_ENTITY_WEB', $defaults['kalion.auth.entities.web']),
            'api' => env('KALION_AUTH_ENTITY_API', $defaults['kalion.auth.entities.api']),
        ],
        'repositories' => [
            'web' => env('KALION_AUTH_REPOSITORY_WEB', $defaults['kalion.auth.repositories.web']),
            'api' => env('KALION_AUTH_REPOSITORY_API', $defaults['kalion.auth.repositories.api']),
        ],
        'services' => [
            'authentication' => env('KALION_AUTH_SERVICE_AUTHENTICATION', $defaults['kalion.auth.services.authentication']),
            'login' => env('KALION_AUTH_SERVICE_LOGIN', $defaults['kalion.auth.services.login']),
            'register' => env('KALION_AUTH_SERVICE_REGISTER', $defaults['kalion.auth.services.register']),
            'password_reset' => env('KALION_AUTH_SERVICE_PASSWORD_RESET', $defaults['kalion.auth.services.password_reset']),
        ],
        'fields' => [
            'web' => env('KALION_AUTH_FIELD', $defaults['kalion.auth.fields.web']),
            'api' => env('KALION_AUTH_FIELD_API', $defaults['kalion.auth.fields.api']),
        ],
        'available_fields' => [
            'id' => [
                'name' => 'id',
                'label' => 'k::text.input.id',
                'type' => 'text',
                'placeholder' => 'Id',
            ],
            'email' => [
                'name' => 'email',
                'label' => 'k::text.input.email',
                'type' => 'email',
                'placeholder' => 'name@company.com', // you@somewhere.com
            ],
            'matricula' => [
                'name' => 'matricula',
                'label' => 'k::text.input.matricula',
                'type' => 'text',
                'placeholder' => 'Matricula',
            ],
            'custom' => [
                'name' => env('KALION_AUTH_FIELD_CUSTOM_NAME', $defaults['kalion.auth.available_fields.custom.name']),
                'label' => env('KALION_AUTH_FIELD_CUSTOM_LABEL', $defaults['kalion.auth.available_fields.custom.label']),
                'type' => env('KALION_AUTH_FIELD_CUSTOM_TYPE', $defaults['kalion.auth.available_fields.custom.type']),
                'placeholder' => env('KALION_AUTH_FIELD_CUSTOM_PLACEHOLDER', $defaults['kalion.auth.available_fields.custom.placeholder']), // you@somewhere.com
            ]
        ],
        'load_roles' => (bool) env('KALION_AUTH_LOAD_ROLES', $defaults['kalion.auth.load_roles']),
        'display_role_in_exception' => (bool) env('KALION_AUTH_DISPLAY_ROLE_IN_EXCEPTION', $defaults['kalion.auth.display_role_in_exception']),
        'display_permission_in_exception' => (bool) env('KALION_AUTH_DISPLAY_PERMISSION_IN_EXCEPTION', $defaults['kalion.auth.display_permission_in_exception']),
    ],

    /*
    |--------------------------------------------------------------------------
    | Processes
    |--------------------------------------------------------------------------
    |
    | The following option allows you to set whether the ProcessChecker class
    | will save the results in the Laravel cache.
    |
    */

    'process' => [
        'status_should_use_cache' => (bool) env('KALION_PROCESS_STATUS_SHOULD_USE_CACHE', $defaults['kalion.process.status_should_use_cache']),
    ],

    /*
    |--------------------------------------------------------------------------
    | Commands
    |--------------------------------------------------------------------------
    |
    | The following options allow you to configure the "kalion:start" command.
    |
    */

    'command' => [
        'start' => [
            'version_node' => env('KALION_COMMAND_START_VERSION_NODE', $defaults['kalion.command.start.version_node']),
            'package_in_develop' => (bool) env('KALION_COMMAND_START_PACKAGE_IN_DEVELOP', $defaults['kalion.command.start.package_in_develop']),
            'keep_migrations_date' => (bool) env('KALION_COMMAND_START_KEEP_MIGRATIONS_DATE', $defaults['kalion.command.start.keep_migrations_date']),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | The following options allow you to configure custom logging levels
    |
    */

    'logging' => [
        'queues_level' => env('LOG_LEVEL', 'debug'),
        'loads_level' => env('LOG_LEVEL', 'debug'),
    ],


    /*
    |--------------------------------------------------------------------------
    | Exceptions
    |--------------------------------------------------------------------------
    |
    | The following options allow you to configure exception behaviors.
    |
    */

    'exceptions' => [
        'http' => [
            'show_logout_form' => (bool) env('KALION_EXCEPTIONS_HTTP_SHOW_LOGOUT_FORM', $defaults['kalion.exceptions.http.show_logout_form']),
        ],
    ],
];
