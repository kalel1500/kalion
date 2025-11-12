<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Layout;

use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\AbstractDataTransferObject;

class LayoutAppDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly string $title,
        public readonly bool   $isFromPackage,
        public readonly bool   $darkMode,
        public readonly bool   $sidebarCollapsed,
        public readonly string $dataTheme,
        public readonly string $colorTheme,
    )
    {
    }
}
