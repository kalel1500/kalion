<?php

namespace Thehouseofel\Kalion\Core\Domain\Concerns\Enums;

trait Nullable
{
    const NULL_VALUE = KALION_ENUM_NULL_VALUE;

    public function isNull(): bool
    {
        return $this === self::null;
    }

    public function isNotNull(): bool
    {
        return ! $this->isNull();
    }
}
