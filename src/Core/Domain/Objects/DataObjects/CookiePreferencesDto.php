<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters\ThemeVo;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
final class CookiePreferencesDto extends AbstractDataTransferObject
{
    public function __construct(
        public string  $version,
        public ThemeVo $theme,
        public bool    $sidebar_collapsed,
        public bool    $sidebar_state_per_page
    )
    {
    }
}
