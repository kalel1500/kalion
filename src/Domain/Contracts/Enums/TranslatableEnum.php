<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Enums;

interface TranslatableEnum
{
    public static function translations(): array;
    public function translate(bool $ucfirst = false): string;
}
