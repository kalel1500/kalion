<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;
use Thehouseofel\Kalion\Domain\Services\TailwindClassFilter;
use Thehouseofel\Kalion\Infrastructure\Console\Commands\ClearAll;
use Thehouseofel\Kalion\Infrastructure\Console\Commands\JobDispatch;
use Thehouseofel\Kalion\Infrastructure\Console\Commands\KalionStart;
use Thehouseofel\Kalion\Infrastructure\Console\Commands\LogsClear;
use Thehouseofel\Kalion\Infrastructure\Console\Commands\PublishAuth;
use Thehouseofel\Kalion\Infrastructure\Console\Commands\ServiceCheck;
use Thehouseofel\Kalion\Infrastructure\Http\Middleware\UserHasPermission;
use Thehouseofel\Kalion\Infrastructure\Http\Middleware\UserHasRole;
use Thehouseofel\Kalion\Infrastructure\Services\Kalion;
use Thehouseofel\Kalion\Infrastructure\Services\Version;

class KalionServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     */
    public array $singletons = [
        'authManager'                                                                          => \Thehouseofel\Kalion\Infrastructure\Services\Auth\AuthManager::class,
        \Thehouseofel\Kalion\Domain\Contracts\Services\CurrentUserContract::class              => \Thehouseofel\Kalion\Infrastructure\Services\Auth\CurrentUser::class,
        \Thehouseofel\Kalion\Domain\Contracts\Repositories\JobRepositoryContract::class        => \Thehouseofel\Kalion\Infrastructure\Repositories\Eloquent\JobRepository::class,
        \Thehouseofel\Kalion\Domain\Contracts\Repositories\RoleRepositoryContract::class       => \Thehouseofel\Kalion\Infrastructure\Repositories\Eloquent\RoleRepository::class,
        \Thehouseofel\Kalion\Domain\Contracts\Repositories\PermissionRepositoryContract::class => \Thehouseofel\Kalion\Infrastructure\Repositories\Eloquent\PermissionRepository::class,
        \Thehouseofel\Kalion\Domain\Contracts\Repositories\StateRepositoryContract::class      => \Thehouseofel\Kalion\Infrastructure\Repositories\Eloquent\StateRepository::class,
    ];

    /**
     * Remove the given provider from the application's provider bootstrap file.
     */
    public static function removeProviderFromBootstrapFile(string $provider, ?string $path = null): bool
    {
        $path ??= app()->getBootstrapProvidersPath();

        if (!file_exists($path)) {
            return false;
        }

        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($path, true);
        }

        // Cargar los proveedores actuales del archivo
        $providers = collect(require $path)
            ->reject(fn($p) => $p === $provider) // Eliminar el provider específico
            ->unique()
            ->sort()
            ->values()
            ->map(fn($p) => '    '.$p.'::class,') // Formatear las líneas
            ->implode(PHP_EOL);

        $content = '<?php

return [
'.$providers.'
];';

        // Escribir el contenido actualizado en el archivo
        file_put_contents($path, $content.PHP_EOL);

        return true;
    }

    private function updateNameOfMigrationsIfExist(): void
    {
        $filesystem = new Filesystem();
        $migrationsPath = database_path('migrations');

        // Lista de nombres de migraciones que quieres renombrar (sin timestamp)
        $migrationFiles = [
            'create_states_table',
            'create_tags_table',
            'create_posts_table',
            'create_comments_table',
            'create_post_tag_table',
        ];

        // Verificar si hay al menos una migración publicada usando coincidencia parcial
        $migrationsExist = collect($filesystem->files($migrationsPath))->some(function ($file) use ($migrationFiles) {
            return collect($migrationFiles)->contains(fn($migration) => Str::contains($file->getFilename(), $migration));
        });

        // Salir si no hay migraciones publicadas
        if (!$migrationsExist) return;

        $timestamp = now(); // Iniciar con el timestamp actual

        foreach ($filesystem->files($migrationsPath) as $file) {
            foreach ($migrationFiles as $migration) {
                if (Str::contains($file->getFilename(), $migration)) {
                    // Generar nuevo nombre con timestamp actual + nombre de la migración
                    $newName = $timestamp->format('Y_m_d_His') . '_' . $migration . '.php';

                    // Renombrar el archivo
                    $filesystem->move($file->getPathname(), $migrationsPath . '/' . $newName);

                    // Incrementar el timestamp en 1 segundo para la próxima migración
                    $timestamp->addSecond();

                    break; // Salimos del bucle interno tras encontrar la coincidencia
                }
            }
        }
    }


    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (! defined('KALION_PATH')) {
            define('KALION_PATH', realpath(__DIR__.'/../../'));
        }

        $this->registerSingletons();
        $this->mergeConfig();
    }

    protected function registerSingletons(): void
    {
        $this->app->alias(\Thehouseofel\Kalion\Domain\Contracts\Services\LayoutContract::class, 'layoutService');
        $this->app->singleton(\Thehouseofel\Kalion\Domain\Contracts\Services\LayoutContract::class, fn($app) => new (Kalion::getClassServiceLayout()));
        $this->app->singleton(\Thehouseofel\Kalion\Domain\Contracts\Services\LoginContract::class, fn($app) => new (Kalion::getClassServiceLogin()));
        $this->app->singleton(\Thehouseofel\Kalion\Domain\Contracts\Services\RegisterContract::class, fn($app) => new (Kalion::getClassServiceRegister()));
        $this->app->singleton(\Thehouseofel\Kalion\Domain\Contracts\Services\PasswordResetContract::class, fn($app) => new (Kalion::getClassServicePasswordReset()));
    }

    /**
     * Setup the configuration for Horizon.
     */
    protected function mergeConfig(): void
    {
        // Configuración - Mergear la configuración del paquete con la configuración de la aplicación, solo hará falta publicar si queremos sobreescribir alguna configuración
        if (!$this->app->configurationIsCached()) {
            $this->mergeConfigFrom(KALION_PATH.'/config/kalion.php', 'kalion');
            $this->mergeConfigFrom(KALION_PATH.'/config/kalion_links.php', 'kalion_links');
        }
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
        if (! $this->app->configurationIsCached()) {
            Kalion::setLogChannels();
            Kalion::setAuthApiGuards();
        }
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
                $this->loadRoutesFrom(KALION_PATH.'/routes/web.php');
            });
        }
    }

    /**
     * Register the Package resources.
     */
    protected function registerResources(): void
    {
        $this->loadViewsFrom(KALION_PATH.'/resources/views', 'kal');
    }

    /**
     * Register the package's publishable resources.
     */
    protected function registerPublishing(): void
    {
        if (!$this->app->runningInConsole()) return;

        /*
         * -------------------
         * --- Migraciones ---
         * -------------------
         */

        if (config('kalion.publish_migrations') && Version::laravelMin9()) {
            $existNewMethod = method_exists($this, 'publishesMigrations');
            $publishesMigrationsMethod = $existNewMethod
                ? 'publishesMigrations'
                : 'publishes';

            $this->{$publishesMigrationsMethod}([
                KALION_PATH.'/database/migrations'                => database_path('migrations'),
                KALION_PATH.'/stubs/generate/database/migrations' => database_path('migrations'),
            ], 'kalion-migrations');

            /*if (!$existNewMethod) {
                Event::listen(function (VendorTagPublished $event) {
                    // Definir que palabras identifican las migraciones del paquete
                    $keywords = ['laravel-kalion-and-ddd-architecture-utilities', 'migrations'];

                    // Buscar en las rutas publicadas si alguna contiene las 3 palabras
                    $publishedKalionMigrations = Arr::first(array_keys($event->paths), fn($key) => collect($keywords)->every(fn($word) => Str::contains($key, $word)));

                    // Actualizar nombres de las migraciones solo si se han ejecutado
                    if ($publishedKalionMigrations) {
                        $this->updateNameOfMigrationsIfExist();
                    }
                });
            }*/
        }


        /*
         * --------------
         * --- Vistas ---
         * --------------
         */

        // Todas
        $this->publishes([
            KALION_PATH.'/resources/views'                    => base_path('resources/views/vendor/kalion'),
            KALION_PATH.'/src/Infrastructure/View/Components' => app_path('View/Components'),
        ], 'kalion-views');

        // Publicar solo la vista "app.blade.php"
        $this->publishes([
            KALION_PATH.'/resources/views/components/layout/app.blade.php'   => base_path('resources/views/vendor/kalion/components/layout/app.blade.php'),
            KALION_PATH.'/src/Infrastructure/View/Components/Layout/App.php' => app_path('View/Components/Layout/App.php'),
        ], 'kalion-view-layout');


        /*
         * -----------------------
         * --- Configuraciones ---
         * -----------------------
         */

        // kalion.php
        $this->publishes([
            KALION_PATH.'/config/kalion.php' => config_path('kalion.php'),
        ], 'kalion-config');

        // kalion_links.php
        $this->publishes([
            KALION_PATH.'/config/kalion_links.php' => config_path('kalion_links.php'),
        ], 'kalion-config-links');


        /*
         * --------------------
         * --- Traducciones ---
         * --------------------
         */

        $langPath = Version::laravelMin9()
            ? $this->app->langPath('vendor/kalion')
            : $this->app->resourcePath('lang/vendor/kalion');
        $this->publishes([
            KALION_PATH.'/lang' => $langPath,
        ], 'kalion-lang');
    }

    /**
     * Register the Package Artisan commands.
     */
    protected function registerCommands(): void
    {
        $this->commands([
            ClearAll::class,
            KalionStart::class,
            PublishAuth::class,
            JobDispatch::class,
            LogsClear::class,
            ServiceCheck::class,
        ]);
    }

    /**
     * Register Package's migration files.
     */
    protected function registerMigrations(): void
    {
        if (
            $this->app->runningInConsole() &&
            config('kalion.run_migrations') &&
            Version::laravelMin9()
        ) {
            $this->loadMigrationsFrom(KALION_PATH.'/database/migrations');
            $this->loadMigrationsFrom(KALION_PATH.'/stubs/generate/database/migrations');
        }
    }

    /**
     * Register Package's migration files.
     */
    protected function registerTranslations(): void
    {
        $this->loadTranslationsFrom(KALION_PATH.'/lang', 'k');
        $this->loadJsonTranslationsFrom(KALION_PATH.'/lang');
    }

    /**
     * Register Package's components files.
     */
    protected function registerComponents(): void
    {
        if (!Version::laravelMin9()) return;

        // Registrar componentes con Clase
        Blade::componentNamespace('Thehouseofel\\Kalion\\Infrastructure\\View\\Components', 'kal');

        // Registrar componentes anónimos
        Blade::anonymousComponentPath(KALION_PATH.'/resources/views/components', 'kal');
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
//        /** @var Kernel $kernel */
//        $kernel = $this->app->make(Kernel::class);

        // Registrar/sobreescribir un grupo de middlewares
//        $router->middlewareGroup('newCustomGroup', [\Vendor\Package\Http\Middleware\KalionAnyMiddleware::class]);

        // Añadir un middleware a un grupo
//        $router->pushMiddlewareToGroup('web', ShareInertiaData::class);

        // Registrar middlewares solo para rutas específicas
        $router->aliasMiddleware('userCan', UserHasPermission::class);
        $router->aliasMiddleware('userIs', UserHasRole::class);

        // El Middleware AddPreferencesCookies al grupo de rutas web
        if (
            !$this->app->runningInConsole() &&
            !empty(config('app.key')) &&
            config('kalion.enable_preferences_cookie')
        ) {
            // Añadir un middleware a un grupo
            $router->pushMiddlewareToGroup('web', \Thehouseofel\Kalion\Infrastructure\Http\Middleware\AddPreferencesCookies::class); // $kernel->appendMiddlewareToGroup('web', \Thehouseofel\Kalion\Infrastructure\Http\Middleware\AddPreferencesCookies::class);

            // Evitar el encriptado de las cookies de las preferencias del usuario
            $this->app->afterResolving(EncryptCookies::class, function (EncryptCookies $middleware) {
                $middleware->disableFor(config('kalion.cookie.name')); // laravel_kalion_user_preferences
            });
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
