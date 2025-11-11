<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Contracts\Enums;

interface Translatable
{
    public static function translations(): array;

    public function translate(bool $ucfirst = false): string;
}
