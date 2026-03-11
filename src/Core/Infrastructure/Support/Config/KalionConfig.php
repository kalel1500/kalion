<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Config;

use Illuminate\Support\Arr;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Config\Redirect\RedirectAfterLogin;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Config\Redirect\RedirectDefaultPath;
use Thehouseofel\Kalion\Features\Auth\Infrastructure\Support\Auth\AuthenticationService;
use Thehouseofel\Kalion\Features\Auth\Infrastructure\Support\Auth\Flow\LoginService;
use Thehouseofel\Kalion\Features\Auth\Infrastructure\Support\Auth\Flow\PasswordResetService;
use Thehouseofel\Kalion\Features\Auth\Infrastructure\Support\Auth\Flow\RegisterService;
use Thehouseofel\Kalion\Features\Components\Domain\Support\BaseLayoutData;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\ApiUserEntity;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\UserEntity;
use Thehouseofel\Kalion\Features\Shared\Infrastructure\Models\ApiUser;
use Thehouseofel\Kalion\Features\Shared\Infrastructure\Models\User;
use Thehouseofel\Kalion\Features\Shared\Infrastructure\Repositories\Eloquent\EloquentApiUserRepository;
use Thehouseofel\Kalion\Features\Shared\Infrastructure\Repositories\Eloquent\EloquentUserRepository;

class KalionConfig
{
    protected static array $defaults     = [
        'kalion.run_migrations'                                                      => false,
        'kalion.register_routes'                                                     => true,
        'kalion.web_middlewares.add_preferences_cookies.active'                      => true,
        'kalion.web_middlewares.force_array_session_in_cloud.active'                 => true,
        'kalion.web_middlewares.force_array_session_in_cloud.cloud_user_agent_value' => 'kube-probe',
        'kalion.default_path'                                                        => null,
        'kalion.broadcasting_enabled'                                                => false,
        'kalion.entity_calculated_props_mode'                                        => 's',
        'kalion.minimum_value_for_id'                                                => 1,
        'kalion.cookie.duration'                                                     => (60 * 24 * 364),
        'kalion.cookie.version'                                                      => '0',
        'kalion.layout.default_theme'                                                => null,
        'kalion.layout.use_elevated_shadows'                                         => false,
        'kalion.layout.navbar_density'                                               => 'normal',
        'kalion.layout.default_sidebar_state'                                        => null,
        'kalion.layout.sidebar_state_per_page'                                       => false,
        'kalion.layout.sidebar_disabled'                                             => false,
        'kalion.layout.show_footer'                                                  => true,
        'kalion.layout.show_debug_main_border'                                       => false,
        'kalion.layout.data_provider'                                                => BaseLayoutData::class,
        'kalion.layout.logo_path'                                                    => 'resources/images/logo.svg',
        'kalion.layout.favicon_path'                                                 => 'resources/images/favicon.ico',
        'kalion.auth.fake'                                                           => false,
        'kalion.auth.disable_register'                                               => false,
        'kalion.auth.disable_password_reset'                                         => false,
        'kalion.auth.redirect_after_login'                                           => null,
        'kalion.auth.blades.fake'                                                    => 'kal::pages.auth.landing',
        'kalion.auth.blades.login'                                                   => 'kal::pages.auth.login',
        'kalion.auth.blades.register'                                                => 'kal::pages.auth.register',
        'kalion.auth.blades.password_reset'                                          => 'kal::pages.auth.password-reset',
        'kalion.auth.models.web'                                                     => User::class,
        'kalion.auth.models.api'                                                     => ApiUser::class,
        'kalion.auth.entities.web'                                                   => UserEntity::class,
        'kalion.auth.entities.api'                                                   => ApiUserEntity::class,
        'kalion.auth.repositories.web'                                               => EloquentUserRepository::class,
        'kalion.auth.repositories.api'                                               => EloquentApiUserRepository::class,
        'kalion.auth.services.authentication'                                        => AuthenticationService::class,
        'kalion.auth.services.login'                                                 => LoginService::class,
        'kalion.auth.services.register'                                              => RegisterService::class,
        'kalion.auth.services.password_reset'                                        => PasswordResetService::class,
        'kalion.auth.fields.web'                                                     => 'email',
        'kalion.auth.fields.api'                                                     => 'name',
        'kalion.auth.available_fields.custom.name'                                   => 'email',
        'kalion.auth.available_fields.custom.label'                                  => 'k::text.input.email',
        'kalion.auth.available_fields.custom.type'                                   => 'email',
        'kalion.auth.available_fields.custom.placeholder'                            => 'name@company.com',
        'kalion.auth.load_roles'                                                     => true,
        'kalion.auth.display_role_in_exception'                                      => false,
        'kalion.auth.display_permission_in_exception'                                => false,
        'kalion.process.status_should_use_cache'                                     => true,
        'kalion.command.start.version_node'                                          => '>=20.11.1',
        'kalion.command.start.package_in_develop'                                    => false,
        'kalion.command.start.keep_migrations_date'                                  => false,
        'kalion.exceptions.http.show_logout_form'                                    => false,
    ];
    protected static array $registry     = [];
    protected static array $priority     = [];
    protected static array $scanPackages = [];

    public static function getDefaults(): array
    {
        return static::$defaults;
    }

    public static function getRegistry(): array
    {
        return static::$registry;
    }

    public static function getOrderedIdentifiers(): array
    {
        $keys = array_keys(static::$registry);

        // Si no hay prioridad definida, devolvemos las keys tal cual
        if (empty(static::$priority)) {
            return $keys;
        }

        // Mapeamos la prioridad a índices [nombre => orden] para búsqueda O(1)
        $priorityOrder = array_flip(static::$priority);

        usort($keys, function ($a, $b) use ($priorityOrder) {
            // Obtenemos la posición o asignamos el final (PHP_INT_MAX)
            $posA = $priorityOrder[$a] ?? PHP_INT_MAX;
            $posB = $priorityOrder[$b] ?? PHP_INT_MAX;

            // El operador <=> devuelve -1, 0 o 1
            return $posA <=> $posB;
        });

        return $keys;
    }

    public static function getScanPackages(): array
    {
        $packages = config('kalion.packages_to_scan_for_jobs');
        $merged   = array_merge(
            is_array($packages) ? $packages : explode(';', $packages),
            static::$scanPackages
        );
        return array_filter($merged); // Limpiar valores vacíos (por el explode)
    }

    public static function setPriority(array $priority): void
    {
        static::$priority = $priority;
    }

    public static function override(array $overrides, string $identifier): void
    {
        static::$registry[$identifier] = array_merge(
            static::$registry[$identifier] ?? [],
            $overrides
        );
    }

    public static function apply(): void
    {
        $defaults = self::getDefaults();

        foreach (self::getOrderedIdentifiers() as $id) {
            foreach (static::$registry[$id] as $key => $class) {
                if (config($key) === $defaults[$key]) {
                    config([$key => $class]);
                }
            }
        }

        // Finalmente, nos aseguramos de que Laravel reciba la clase final
        config([
            'auth.providers.users.model'     => config('kalion.auth.models.web'),
            'auth.providers.api_users.model' => config('kalion.auth.models.api'),
        ]);
    }

    public static function registerPackagesToScanJobs(string|array $packages): void
    {
        static::$scanPackages = array_merge(
            static::$scanPackages,
            Arr::wrap($packages)
        );
    }

    public static function redirectTo(callable|string|null $defaultPath = null, callable|string|null $afterLogin = null): void
    {
        $defaultPath = is_string($defaultPath) ? fn() => $defaultPath : $defaultPath;
        $afterLogin  = is_string($afterLogin) ? fn() => $afterLogin : $afterLogin;

        if ($defaultPath) {
            RedirectDefaultPath::redirectUsing($defaultPath);
        }

        if ($afterLogin) {
            RedirectAfterLogin::redirectUsing($afterLogin);
        }

    }
}
