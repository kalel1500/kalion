<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support;

use BadMethodCallException;
use Thehouseofel\Kalion\Core\Domain\Concerns\InteractsWithContainer;

/**
 * Framework-agnostic reader of the configuration used by the domain.
 *
 * Reads the Laravel config when the container is available and falls back to DEFAULTS otherwise.
 * In tests, `fake()` overrides any key (with or without Laravel) until `reset()` is called.
 *
 * @method string appEnv()
 * @method bool appDebug()
 * @method bool authEnabled()
 * @method bool abilitiesEnabled()
 * @method int minimumValueForId()
 * @method bool displayRoleInException()
 * @method bool displayPermissionInException()
 * @method bool exceptionsShowLogoutForm()
 * @method string entityCalculatedPropsMode()
 */
class KalionConfigReader
{
    use InteractsWithContainer;

    /**
     * method => [config key, default value].
     * The "kalion.*" defaults must match KalionConfigManager::$defaults and the "app.*" ones the Laravel defaults.
     */
    private const DEFAULTS = [
        'appEnv'                       => ['app.env', 'production'],
        'appDebug'                     => ['app.debug', false],
        'authEnabled'                  => ['kalion.auth.enabled', true],
        'abilitiesEnabled'             => ['kalion.auth.abilities_enabled', true],
        'minimumValueForId'            => ['kalion.minimum_value_for_id', 1],
        'displayRoleInException'       => ['kalion.auth.display_role_in_exception', false],
        'displayPermissionInException' => ['kalion.auth.display_permission_in_exception', false],
        'exceptionsShowLogoutForm'     => ['kalion.exceptions.http.show_logout_form', false],
        'entityCalculatedPropsMode'    => ['kalion.entity_calculated_props_mode', 's'],
    ];

    private array $overrides = [];

    /**
     * Read any config key directly (e.g. a dynamic key or a key from another package).
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->overrides)) {
            return $this->overrides[$key];
        }

        if (self::hasBinding('config')) {
            return self::resolveBinding('config')->get($key, $default);
        }

        return $default;
    }

    /**
     * Override config keys (e.g. ['kalion.minimum_value_for_id' => 0]). Intended for tests.
     */
    public function fake(array $values): static
    {
        $this->overrides = array_merge($this->overrides, $values);

        return $this;
    }

    public function reset(): static
    {
        $this->overrides = [];

        return $this;
    }

    public function __call(string $method, array $arguments): mixed
    {
        if (! isset(self::DEFAULTS[$method])) {
            throw new BadMethodCallException(sprintf('Method %s::%s() does not exist.', static::class, $method));
        }

        [$key, $default] = self::DEFAULTS[$method];

        $value = $this->get($key, $default);

        // Cast to the type of the default value to ensure consistent return types
        return match (gettype($default)) {
            'integer' => (int)$value,
            'boolean' => (bool)$value,
            'string'  => (string)$value,
            'double'  => (float)$value,
            'array'   => (array)$value,
            default   => $value,
        };
    }
}
