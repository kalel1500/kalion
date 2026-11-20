<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain;

use BadMethodCallException;
use Thehouseofel\Kalion\Core\Domain\Concerns\InteractsWithContainer;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\EnvVo;
use Thehouseofel\Kalion\Core\Domain\Support\KalionConfigReader;

/**
 * Framework-agnostic part of the `kalion()` helper (config, translations and environment).
 *
 * With Laravel, `kalion()` resolves the full `Kalion` class from the container (registered by the
 * KalionServiceProvider). Without Laravel (plain unit tests), it returns this class.
 */
class KalionContext
{
    use InteractsWithContainer;

    private static ?self $fallback = null;

    protected ?KalionConfigReader $config = null;

    /**
     * @return \Thehouseofel\Kalion\Core\Infrastructure\Kalion
     */
    public static function instance(): self
    {
        if (self::hasBinding(self::class)) {
            return self::resolveBinding(self::class);
        }

        return self::$fallback ??= new self();
    }

    /**
     * Forget the instance used without Laravel (including the faked config values).
     */
    public static function flushState(): void
    {
        self::$fallback = null;
    }

    public function config(): KalionConfigReader
    {
        return $this->config ??= new KalionConfigReader();
    }

    public function trans(?string $key, array $replace = [], ?string $locale = null): string|array|null
    {
        if (is_null($key)) {
            return null;
        }

        if (self::hasBinding('translator')) {
            return self::resolveBinding('translator')->get($key, $replace, $locale);
        }

        $replacements = [];
        foreach ($replace as $search => $value) {
            $search = (string)$search;
            $value  = (string)$value;

            $replacements[':' . $search]             = $value;
            $replacements[':' . ucfirst($search)]    = ucfirst($value);
            $replacements[':' . strtoupper($search)] = strtoupper($value);
        }

        return strtr($key, $replacements);
    }

    public function env(): EnvVo
    {
        return EnvVo::from($this->config()->appEnv());
    }

    public function isProd(): bool
    {
        return $this->env()->isProd();
    }

    public function isLocal(): bool
    {
        return $this->env()->isLocal();
    }

    public function isPre(): bool
    {
        return $this->env()->isPre();
    }

    public function isTesting(): bool
    {
        return $this->env()->isTesting();
    }

    public function isDebug(): bool
    {
        return $this->config()->appDebug();
    }

    public function __call(string $method, array $arguments): mixed
    {
        $message = method_exists('Thehouseofel\Kalion\Core\Infrastructure\Kalion', $method)
            ? sprintf('The "kalion()->%s()" method requires Laravel (the KalionServiceProvider must be registered).', $method)
            : sprintf('Method %s::%s() does not exist.', static::class, $method);

        throw new BadMethodCallException($message);
    }
}
