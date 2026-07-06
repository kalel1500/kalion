<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support\Reflection\Dto;

readonly class DisabledData
{
    public function __construct(
        public bool $isDisabled,
        public bool $useJsonSerialization,
    )
    {
    }
}

