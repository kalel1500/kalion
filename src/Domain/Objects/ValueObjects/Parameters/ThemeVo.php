<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
enum ThemeVo: string
{
    case dark   = 'dark';
    case light  = 'light';
    case system = 'system';

    public function isDark(): bool
    {
        return ($this === self::dark);
    }

    public function isLight(): bool
    {
        return ($this === self::light);
    }

    public function isSystem(): bool
    {
        return ($this === self::system);
    }

    public function getDataTheme(): string
    {
        return $this->isSystem() ? '' : $this->value;
    }

    public static function getDefault(): ThemeVo
    {
        return self::system;
    }
}
