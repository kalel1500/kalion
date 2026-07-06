<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support\Reflection\Dto;

readonly class ConstructorParams
{
    /**
     * @param array<int, ParamMetadata> $make
     * @param array<int, ParamMetadata> $props
     */
    public function __construct(
        public array $make,
        public array $props,
    )
    {
    }
}

