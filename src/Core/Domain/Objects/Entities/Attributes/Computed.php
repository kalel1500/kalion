<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class Computed
{
    public const AS_ATTRIBUTE = 'addAlways';

    public function __construct(
        public readonly string|array $contexts = [],
        public readonly bool         $addOnFull = false,
    )
    {
    }
}
