<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\SidebarState;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\ThemeVo;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
final class CookiePreferencesDto extends AbstractDataTransferObject
{
    public function __construct(
        public string       $version,
        public ThemeVo      $theme,
        public SidebarState $sidebar_state,
        public bool         $sidebar_state_per_page
    )
    {
    }
}
