<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class UseMethod
{
    public function __construct(public readonly string $method) {}
}
