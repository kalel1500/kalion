<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class CollectionOf
{
    public function __construct(public string $class) {}
}
