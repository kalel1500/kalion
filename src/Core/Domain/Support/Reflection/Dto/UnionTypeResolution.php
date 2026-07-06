<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support\Reflection\Dto;

readonly class UnionTypeResolution
{
    public function __construct(
        public string  $typeName,
        public ?string $class,
        public bool    $allowsNull,
        public bool    $isId,
    )
    {
    }
}

