<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\EnvVo;

class Kalion
{
    protected ?EnvVo $environment = null;

    public function environment(): EnvVo
    {
        return $this->environment ??= EnvVo::from(config('app.env'));
    }

    public function isProd(): bool
    {
        return $this->environment()->isProd();
    }

    public function isLocal(): bool
    {
        return $this->environment()->isLocal();
    }

    public function isPre(): bool
    {
        return $this->environment()->isPre();
    }

    public function isTesting(): bool
    {
        return $this->environment()->isTesting();
    }
}
