<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final class Computed
{
    public array $contexts;

    public function __construct(string ...$contexts)
    {
        $this->contexts = $contexts;
    }
}
