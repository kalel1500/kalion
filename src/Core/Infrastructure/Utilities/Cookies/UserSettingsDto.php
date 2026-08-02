<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cookies;

use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\AbstractDataTransferObject;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\SidebarState;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\ThemeVo;

/**
 * @internal This class is intended for internal package usage only.
 */
class UserSettingsDto extends AbstractDataTransferObject
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
