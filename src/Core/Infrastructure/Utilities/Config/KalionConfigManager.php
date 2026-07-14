<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Config;

use Illuminate\Support\Arr;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Config\Redirect\RedirectAfterLogin;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Config\Redirect\RedirectDefaultPath;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\ApiUserEntity;
use Thehouseofel\Kalion\Features\Auth\Domain\Objects\Entities\UserEntity;
use Thehouseofel\Kalion\Features\Auth\Infrastructure\EntityGuard;
use Thehouseofel\Kalion\Features\Auth\Infrastructure\Models\ApiUser;
use Thehouseofel\Kalion\Features\Auth\Infrastructure\Models\User;
use Thehouseofel\Kalion\Features\Auth\Infrastructure\Repositories\Eloquent\EloquentApiUserRepository;
use Thehouseofel\Kalion\Features\Auth\Infrastructure\Repositories\Eloquent\EloquentUserRepository;
use Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Actions\AuthenticateUser;
use Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Actions\CreateNewUser;
use Thehouseofel\Kalion\Features\AuthFlow\Infrastructure\Actions\ResetUserPassword;
use Thehouseofel\Kalion\Features\Components\Domain\Support\BaseLayoutData;

class KalionConfigManager
{
    protected static array $defaults = [
        'kalion.provider.run_migrations'                                 => false,
        'kalion.provider.register_routes'                                => true,
        'kalion.provider.web_middlewares.add_preferences_cookies.active' => true,
        'kalion.provider.stateless_requests.paths'                       => '',
        'kalion.provider.stateless_requests.user_agents'                 => '',
        'kalion.default_path'                                            => null,
        'kalion.broadcasting_enabled'                                    => false,
        'kalion.entity_calculated_props_mode'                            => 's',
        'kalion.minimum_value_for_id'                                    => 1,
        'kalion.cookie.duration'                                         => (60 * 24 * 364),
        'kalion.cookie.version'                                          => '0',
        'kalion.layout.default_theme'                                    => null,
        'kalion.layout.use_elevated_shadows'                             => false,
        'kalion.layout.navbar_density'                                   => 'normal',
        'kalion.layout.navbar_title_spacing'                             => 'none',
        'kalion.layout.default_sidebar_state'                            => null,
        'kalion.layout.sidebar_state_per_page'                           => false,
        'kalion.layout.sidebar_disabled'                                 => false,
        'kalion.layout.show_footer'                                      => true,
        'kalion.layout.show_debug_main_border'                           => false,
        'kalion.layout.data_provider'                                    => BaseLayoutData::class,
        'kalion.layout.logo_path'                                        => 'resources/images/logo.svg',
        'kalion.layout.favicon_path'                                     => 'resources/images/favicon.ico',
        'kalion.auth.fake'                                               => false,
        'kalion.auth.show_register_link'                                 => true,
        'kalion.auth.show_password_reset_link'                           => true,
        'kalion.auth.redirect_after_login'                               => null,
        'kalion.auth.blades.fake'                                        => 'kal::pages.auth.landing',
        'kalion.auth.blades.login'                                       => 'kal::pages.auth.login',
        'kalion.auth.blades.register'                                    => 'kal::pages.auth.register',
        'kalion.auth.blades.forgot_password'                             => 'kal::pages.auth.forgot-password',
        'kalion.auth.blades.reset_password'                              => 'kal::pages.auth.reset-password',
        'kalion.auth.models.web'                                         => User::class,
        'kalion.auth.models.api'                                         => ApiUser::class,
        'kalion.auth.entities.web'                                       => UserEntity::class,
        'kalion.auth.entities.api'                                       => ApiUserEntity::class,
        'kalion.auth.repositories.web'                                   => EloquentUserRepository::class,
        'kalion.auth.repositories.api'                                   => EloquentApiUserRepository::class,
        'kalion.auth.guard'                                              => EntityGuard::class,
        'kalion.auth.actions.authenticate_user'                          => AuthenticateUser::class,
        'kalion.auth.actions.create_new_user'                            => CreateNewUser::class,
        'kalion.auth.actions.reset_user_password'                        => ResetUserPassword::class,
        'kalion.auth.fields.web'                                         => 'email',
        'kalion.auth.fields.api'                                         => 'name',
        'kalion.auth.available_fields.custom.name'                       => 'email',
        'kalion.auth.available_fields.custom.label'                      => 'k::text.input.email',
        'kalion.auth.available_fields.custom.type'                       => 'email',
        'kalion.auth.available_fields.custom.placeholder'                => 'name@company.com',
        'kalion.auth.load_roles'                                         => true,
        'kalion.auth.display_role_in_exception'                          => false,
        'kalion.auth.display_permission_in_exception'                    => false,
        'kalion.process.status_should_use_cache'                         => true,
        'kalion.command.start.version_node'                              => '>=20.11.1',
        'kalion.command.start.package_in_develop'                        => false,
        'kalion.command.start.keep_migrations_date'                      => false,
        'kalion.exceptions.http.show_logout_form'                        => false,
        'kalion.cooldown.cache_store'                                    => null,
    ];

    protected array $registry     = [];
    protected array $priority     = [];
    protected array $scanPackages = [];
    protected array $afterApply   = [];
    protected bool  $applied      = false;

    public static function getDefaults(): array
    {
        return static::$defaults;
    }

    public function getRegistry(): array
    {
        return $this->registry;
    }

    public function getOrderedIdentifiers(): array
    {
        $keys = array_keys($this->registry);

        // Si no hay prioridad definida, devolvemos las keys tal cual
        if (empty($this->priority)) {
            return $keys;
        }

        // Mapeamos la prioridad a índices [nombre => orden] para búsqueda O(1)
        $priorityOrder = array_flip($this->priority);

        usort($keys, function (string $a, string $b) use ($priorityOrder): int {
            // Obtenemos la posición o asignamos el final (PHP_INT_MAX)
            $posA = $priorityOrder[$a] ?? PHP_INT_MAX;
            $posB = $priorityOrder[$b] ?? PHP_INT_MAX;

            // El operador <=> devuelve -1, 0 o 1
            return $posA <=> $posB;
        });

        return $keys;
    }

    public function getScanPackages(): array
    {
        $packages = config('kalion.packages_to_scan_for_jobs');
        $merged   = array_merge(
            is_array($packages) ? $packages : explode(';', (string)$packages),
            $this->scanPackages
        );

        return array_values(array_filter($merged));
    }

    public function setPriority(array $priority): void
    {
        $this->priority = $priority;
    }

    public function override(array $overrides, string $identifier): void
    {
        $this->registry[$identifier] = array_merge(
            $this->registry[$identifier] ?? [],
            $overrides
        );
    }

    public function apply(): void
    {
        $defaults = self::getDefaults();

        foreach ($this->getOrderedIdentifiers() as $id) {
            foreach ($this->registry[$id] as $key => $value) {
                if (! array_key_exists($key, $defaults)) {
                    continue;
                }

                if (config($key) === $defaults[$key]) {
                    config([$key => $value]);
                }
            }
        }

        $this->applied = true;

        foreach ($this->afterApply as $callback) {
            $callback();
        }

        // Limpiar callbacks para evitar ejecuciones duplicadas en apply() posteriores
        $this->afterApply = [];
    }

    public function afterApply(callable $callback): void
    {
        if ($this->applied) {
            $callback();
            return;
        }

        $this->afterApply[] = $callback;
    }

    public function registerPackagesToScanJobs(string|array $packages): void
    {
        $this->scanPackages = array_merge(
            $this->scanPackages,
            Arr::wrap($packages)
        );
    }

    public function redirectTo(callable|string|null $defaultPath = null, callable|string|null $afterLogin = null): void
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
