<?php

namespace Thehouseofel\Kalion\Domain\Concerns\Enums;

trait HasTranslations
{
    public function translate(bool $ucfirst = false): string
    {
        $value = static::translations()[$this->value];
        return ($ucfirst) ? ucfirst($value) : $value;
    }
}
