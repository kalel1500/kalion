<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class DisableReflection
{
    public function __construct(
        public bool $useJsonSerialization = false,
    )
    {
    }
}
