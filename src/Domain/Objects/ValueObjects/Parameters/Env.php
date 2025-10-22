<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts\AbstractEnumVo;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
final class Env extends AbstractEnumVo
{
    const local         = 'local';
    const preproduction = 'preproduction';
    const production    = 'production';
    const testing       = 'testing';

    private bool $isTesting = false;

    protected ?array $permittedValues = [
        self::local,
        self::preproduction,
        self::production,
    ];

    public function __construct(string $value)
    {
        if ($value === static::testing) {
            $this->isTesting = true;
            $value = config('kalion.real_env_in_tests');
        }
        parent::__construct($value);
    }

    public static function from($value = null): static
    {
        $value = $value ?? config('app.env');
        return new static($value);
    }

    public function isLocal(): bool
    {
        return ($this->value === static::local);
    }

    public function isPre(): bool
    {
        return ($this->value === static::preproduction);
    }

    public function isProd(): bool
    {
        return ($this->value === static::production);
    }

    public function isTest(): bool
    {
        return $this->isTesting;
    }
}
