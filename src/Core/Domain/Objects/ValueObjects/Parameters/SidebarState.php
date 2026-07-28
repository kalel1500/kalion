<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters;

use Thehouseofel\Kalion\Core\Domain\Concerns\Enums\HasFromOr;

/**
 * @internal This class is intended for internal package usage only.
 */
enum SidebarState: string
{
    use HasFromOr;

    case expanded  = 'expanded';
    case collapsed = 'collapsed';

    public function isExpanded(): bool
    {
        return ($this === self::expanded);
    }

    public function isCollapsed(): bool
    {
        return ($this === self::collapsed);
    }

    public static function getDefault(): SidebarState
    {
        return self::expanded;
    }
}
