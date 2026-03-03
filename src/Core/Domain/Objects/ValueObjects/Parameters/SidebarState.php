<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
enum SidebarState: string
{
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
