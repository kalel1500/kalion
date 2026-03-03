<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters;

use Thehouseofel\Kalion\Core\Domain\Concerns\Enums\HasFromOr;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
enum ThemeVo: string
{
    use HasFromOr;

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
