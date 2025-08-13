<?php

namespace Thehouseofel\Kalion\Domain\Traits;

trait HasTranslations
{
    public function translate(bool $ucfirst = false): string
    {
        $value = static::translations()[$this->value];
        return ($ucfirst) ? ucfirst($value) : $value;
    }
}
