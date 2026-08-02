<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cookies\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class SkipCookieEncryption
{
}

