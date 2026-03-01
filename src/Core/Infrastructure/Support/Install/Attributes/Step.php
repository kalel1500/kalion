<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Step
{
    public const BASE     = 'base';
    public const EXAMPLES = 'examples';
    public const BOTH     = 'both';

    public function __construct(
        public string|array $paths,
        public string       $title,
        public bool         $skip,
        public ?string      $getPathFrom,
    )
    {
    }
}
