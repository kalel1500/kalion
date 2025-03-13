<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Contracts\ContractEnumVo;

final class EnvVo extends ContractEnumVo
{
    const local         = 'local';
    const preproduction = 'preproduction';
    const production    = 'production';
    const testing       = 'testing';

    protected ?array $permittedValues = [
        self::local,
        self::preproduction,
        self::production,
    ];

    public function __construct(string $value)
    {
        if ($value === static::testing) {
            $value = get_environment_real();
        }
        parent::__construct($value);
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
}
