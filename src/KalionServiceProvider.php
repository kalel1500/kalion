<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;
use Thehouseofel\Kalion\Core\Domain\Services\TailwindClassFilter;
use Thehouseofel\Kalion\Core\Infrastructure\Console\Commands\ClearAll;
use Thehouseofel\Kalion\Core\Infrastructure\Console\Commands\ConfigCheck;
use Thehouseofel\Kalion\Core\Infrastructure\Console\Commands\JobDispatch;
use Thehouseofel\Kalion\Core\Infrastructure\Console\Commands\KalionStart;
use Thehouseofel\Kalion\Core\Infrastructure\Console\Commands\LogsClear;
use Thehouseofel\Kalion\Core\Infrastructure\Console\Commands\ProcessCheck;
use Thehouseofel\Kalion\Core\Infrastructure\Console\Commands\PublishAuth;
use Thehouseofel\Kalion\Core\Infrastructure\Http\Middleware\UserHasPermission;
use Thehouseofel\Kalion\Core\Infrastructure\Http\Middleware\UserHasRole;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Config\KalionConfig;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Kalion;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Version;

class KalionServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     */
    public array $singletons = [
        'thehouseofel.kalion.redirectAfterLogin'                                                       => \Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Config\Redirect\RedirectAfterLogin::class,
        'thehouseofel.kalion.redirectDefaultPath'                                                      => \Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Config\Redirect\RedirectDefaultPath::class,
        'thehouseofel.kalion.processChecker'                                                           => \Thehouseofel\Kalion\Core\Infrastructure\Support\Services\ProcessChecker::class,
        \Thehouseofel\Kalion\Features\Shared\Domain\Contracts\Repositories\TabulatorRepository::class  => \Thehouseofel\Kalion\Features\Shared\Infrastructure\Repositories\Eloquent\EloquentTabulatorRepository::class,
        \Thehouseofel\Kalion\Features\Shared\Domain\Contracts\Repositories\JobRepository::class        => \Thehouseofel\Kalion\Features\Shared\Infrastructure\Repositories\Eloquent\EloquentJobRepository::class,
        \Thehouseofel\Kalion\Features\Shared\Domain\Contracts\Repositories\RoleRepository::class       => \Thehouseofel\Kalion\Features\Shared\Infrastructure\Repositories\Eloquent\EloquentRoleRepository::class,
        \Thehouseofel\Kalion\Features\Shared\Domain\Contracts\Repositories\PermissionRepository::class => \Thehouseofel\Kalion\Features\Shared\Infrastructure\Repositories\Eloquent\EloquentPermissionRepository::class,
        \Thehouseofel\Kalion\Features\Shared\Domain\Contracts\Repositories\StatusRepository::class     => \Thehouseofel\Kalion\Features\Shared\Infrastructure\Repositories\Eloquent\EloquentStatusRepository::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (! defined('KALION_PATH')) {
            define('KALION_PATH', realpath(__DIR__ . '/../'));
        }

        $this->registerSingletons();
        $this->mergeConfig();
    }

    protected function registerSingletons(): void
    {
        $this->app->singleton(\Thehouseofel\Kalion\Features\Components\Domain\Services\Contracts\LayoutData::class, fn($app) => new (Kalion::getClassServiceLayout()));
        $this->app->singleton(\Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Auth\Contracts\Authentication::class, fn($app) => new (Kalion::getClassServiceAuthentication()));
        $this->app->singleton(\Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Auth\Contracts\Login::class, fn($app) => new (Kalion::getClassServiceLogin()));
        $this->app->singleton(\Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Auth\Contracts\Register::class, fn($app) => new (Kalion::getClassServiceRegister()));
        $this->app->singleton(\Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Auth\Contracts\PasswordReset::class, fn($app) => new (Kalion::getClassServicePasswordReset()));
        $this->app->singleton(\Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Auth\Contracts\AuthenticationFlow::class, \Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Auth\AuthenticationFlowService::class);
    }

    /**
     * Setup the configuration for Horizon.
     */
    protected function mergeConfig(): void
    {
        // Configuración - Mergear la configuración del paquete con la configuración de la aplicación, solo hará falta publicar si queremos sobreescribir alguna configuración
        $this->mergeConfigFrom(KALION_PATH . '/config/kalion.php', 'kalion');
        $this->mergeConfigFrom(KALION_PATH . '/config/kalion_links.php', 'kalion_links');
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->setConfig();
        $this->registerRoutes();
        $this->registerResources();
        $this->registerPublishing();
        $this->registerCommands();
        $this->registerMigrations();
        $this->registerTranslations();
        $this->registerComponents();
        $this->registerBladeDirectives();
        $this->registerMiddlewares();
        $this->registerMacros();
    }

    protected function setConfig(): void
    {
        // 1. Inyectar Logs (Sin que el usuario haga nada)
        config([
            'logging.channels.queues' => array_merge([
                'driver'               => 'single',
                'path'                 => storage_path('logs/queues.log'),
                'level'                => config('kalion.logging.queues_level'),
                'replace_placeholders' => true,
            ], config('logging.channels.queues', [])),

            'logging.channels.loads' => array_merge([
                'driver'               => 'single',
                'path'                 => storage_path('logs/loads.log'),
                'level'                => config('kalion.logging.queues_level'),
                'replace_placeholders' => true,
            ], config('logging.channels.loads', [])),
        ]);


        // 2. Inyectar Auth Model
        config(['auth.providers.users.model' => config('kalion.auth.models.web', config('auth.providers.users.model'))]);

        // 3. Inyectar Guard y Provider para la api
        config([
            'auth.guards.api' => array_merge([
                'driver'   => 'session',
                'provider' => 'api_users',
            ], config('auth.guards.api', [])),

            'auth.providers.api_users' => array_merge([
                'driver' => 'eloquent',
                'model'  => config('kalion.auth.models.api'),
            ], config('auth.providers.api_users', []))
        ]);

        // 4. Aplicar las posibles sobreescrituras de configuración hechas por otros paquetes o la propia app
        $this->app->booted(function () {
            KalionConfig::apply();
        });
    }

    /**
     * Register the Package routes.
     */
    protected function registerRoutes(): void
    {
        if (config('kalion.register_routes')) {
            Route::group([
//                'as' => 'kalion.',
//                'prefix' => 'kalion',
                'middleware' => 'web',
            ], function () {
                $this->loadRoutesFrom(KALION_PATH . '/routes/web.php');
            });
        }
    }

    /**
     * Register the Package resources.
     */
    protected function registerResources(): void
    {
        $this->loadViewsFrom(KALION_PATH . '/resources/views', 'kal');
    }

    /**
     * Register the package's publishable resources.
     */
    protected function registerPublishing(): void
    {
        if (! $this->app->runningInConsole()) return;

        /*
         * -------------------
         * --- Migraciones ---
         * -------------------
         */

        $this->publishesMigrations([
            KALION_PATH . '/database/migrations' => database_path('migrations'),
        ], 'kalion-migrations');


        /*
         * --------------
         * --- Vistas ---
         * --------------
         */

        // Todas
        $this->publishes([
            KALION_PATH . '/resources/views' => base_path('resources/views/vendor/kal'),
        ], 'kalion-views');

        // Publicar solo la vista "app.blade.php"
        $this->publishes([
            KALION_PATH . '/resources/views/components/layout/app.blade.php' => base_path('resources/views/vendor/kal/components/layout/app.blade.php'),
        ], 'kalion-view-layout');


        /*
         * -----------------------
         * --- Configuraciones ---
         * -----------------------
         */

        // kalion.php
        $this->publishes([
            KALION_PATH . '/config/kalion.php' => config_path('kalion.php'),
        ], 'kalion-config');

        // kalion_links.php
        $this->publishes([
            KALION_PATH . '/config/kalion_links.php' => config_path('kalion_links.php'),
        ], 'kalion-config-links');


        /*
         * --------------------
         * --- Traducciones ---
         * --------------------
         */

        $this->publishes([
            KALION_PATH . '/lang' => $this->app->langPath('vendor/kalion'),
        ], 'kalion-lang');
    }

    /**
     * Register the Package Artisan commands.
     */
    protected function registerCommands(): void
    {
        $this->commands([
            ClearAll::class,
            ConfigCheck::class,
            KalionStart::class,
            PublishAuth::class,
            JobDispatch::class,
            LogsClear::class,
            ProcessCheck::class,
        ]);
    }

    /**
     * Register Package's migration files.
     */
    protected function registerMigrations(): void
    {
        if ($this->app->runningInConsole() && config('kalion.run_migrations')) {
            $this->loadMigrationsFrom(KALION_PATH . '/database/migrations');
        }
    }

    /**
     * Register Package's migration files.
     */
    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(KALION_PATH . '/lang', 'k');
        $this->loadJsonTranslationsFrom(KALION_PATH . '/lang');
    }

    /**
     * Register Package's components files.
     */
    protected function registerComponents(): void
    {
        // Registrar componentes anónimos
        Blade::anonymousComponentPath(KALION_PATH . '/resources/views/components', 'kal');
    }

    /**
     * Register Package's Blade directives.
     */
    protected function registerBladeDirectives(): void
    {
        Blade::directive('viteAsset', function ($path) {
            return "<?php
                try {
                    echo e(\\Illuminate\\Support\\Facades\\Vite::asset(trim($path, '\'\"')));
                } catch (\\Throwable \$e) {
                    echo e(\$e->getMessage());
                }
            ?>";
        });
    }

    /**
     * Register Package's Middlewares.
     *
     * @throws BindingResolutionException
     */
    protected function registerMiddlewares(): void
    {
        /** @var Router $router */
        $router = $this->app->make(Router::class);

        // Registrar middlewares solo para rutas específicas
        $router->aliasMiddleware('userCan', UserHasPermission::class);
        $router->aliasMiddleware('userIs', UserHasRole::class);

        if ($this->app->runningInConsole()) return;

        // Registrar/sobreescribir un grupo de middlewares
//        $router->middlewareGroup('newCustomGroup', [\Vendor\Package\Http\Middleware\KalionAnyMiddleware::class]);

        // Añadir middlewares al final de un grupo
//        $router->pushMiddlewareToGroup('web', ShareInertiaData::class); // $kernel = $this->app->make(Kernel::class); $kernel->appendMiddlewareToGroup('web', ShareInertiaData::class);

        // Añadir middlewares al principio de un grupo
//        $router->prependMiddlewareToGroup('web', ShareInertiaData::class);

        // Añadir el Middleware AddPreferencesCookies al grupo de rutas web
        if (config('kalion.web_middlewares.add_preferences_cookies.active')) {
            // Añadir middlewares al final de un grupo
            $router->pushMiddlewareToGroup('web', \Thehouseofel\Kalion\Core\Infrastructure\Http\Middleware\AddPreferencesCookies::class);

            // Evitar el encriptado de las cookies de las preferencias del usuario
            if (! empty(config('app.key'))) {
                $this->app->afterResolving(EncryptCookies::class, function (EncryptCookies $middleware) {
                    $middleware->disableFor(config('kalion.cookie.name')); // laravel_kalion_user_preferences
                });
            }
        }

        // Añadir el Middleware ForceArraySessionInCloud al grupo de rutas web
        if (config('kalion.web_middlewares.force_array_session_in_cloud.active')) {
            // Añadir middlewares al principio de un grupo
            $router->prependMiddlewareToGroup('web', \Thehouseofel\Kalion\Core\Infrastructure\Http\Middleware\ForceArraySessionInCloud::class);
        }
    }

    /**
     * Add Package's Macros.
     */
    protected function registerMacros(): void
    {
        ComponentAttributeBag::macro('mergeTailwind', function ($defaultClasses) {
            /** @var ComponentAttributeBag $this */

            // Obtiene las clases personalizadas
            $customClasses = $this->get('class', '');

            // Eliminar las clases de $defaultClasses que ya vienen en $customClasses
            $filteredDefault = TailwindClassFilter::new()->filter($defaultClasses, $customClasses);

            // Llama al método `merge` de la clase original
            return $this->merge(['class' => $filteredDefault]);
        });

    }
}
