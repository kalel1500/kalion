<?php

namespace Thehouseofel\Kalion\Core\Domain\Concerns\Enums;

use Thehouseofel\Kalion\Core\Infrastructure\Support\Services\Kalion;

trait Nullable
{
    const NULL_VALUE = Kalion::ENUM_NULL_VALUE;

    public function isNull(): bool
    {
        return $this === self::null;
    }

    public function isNotNull(): bool
    {
        return ! $this->isNull();
    }
}
