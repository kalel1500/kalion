<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Title
{
    public array $values;

    public function __construct(...$values)
    {
        $first        = $values[0] ?? [];
        $this->values = is_array($first) ? $first : $values;
    }
}
