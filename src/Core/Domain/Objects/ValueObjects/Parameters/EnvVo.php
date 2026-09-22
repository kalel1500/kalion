<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\Abstracts\Base\AbstractStringVo;

/**
 * @internal This class is intended for internal package usage only.
 */
class EnvVo extends AbstractStringVo
{
    const local         = 'local';
    const preproduction = 'preproduction';
    const production    = 'production';
    const testing       = 'testing';

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

    public function isTesting(): bool
    {
        return ($this->value === static::testing);
    }
}
