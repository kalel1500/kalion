<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_PARAMETER)]
final class WithParams
{
    public readonly ?array $params;

    public function __construct(...$params)
    {
        $first        = $params[0] ?? null;
        $this->params = is_array($first) ? $first : $params;
    }
}
