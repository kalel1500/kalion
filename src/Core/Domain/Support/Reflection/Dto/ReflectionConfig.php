<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Support\Reflection\Dto;

readonly class ReflectionConfig
{
    public function __construct(
        public bool   $props_from_public,
        public bool   $allow_id_union,
        public bool   $allow_disable_reflection,
        public string $expected_exception_class,
    )
    {
    }
}
