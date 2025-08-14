<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Abstracts\AbstractEnumVo;

final class ThemeVo extends AbstractEnumVo
{
    const dark   = 'dark';
    const light  = 'light';
    const system = 'system';

    protected ?array $permittedValues = [
        self::dark,
        self::light,
        self::system,
    ];


    public function isDark(): bool
    {
        return ($this->value === static::dark);
    }

    public function isLight(): bool
    {
        return ($this->value === static::light);
    }

    public function isSystem(): bool
    {
        return ($this->value === static::system);
    }

    public function getDataTheme(): string
    {
        return $this->isSystem() ? '' : $this->value();
    }
}
