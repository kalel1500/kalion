<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Components\Domain\Services\Contracts;

use Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Layout\UserInfoDto;
use Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Navbar\Items\Collections\NavbarItemCollection;

interface LayoutData
{
    public function getMessageCounter(): int;

    public function getNavbarNotifications(): NavbarItemCollection;

    public function getUserInfo(): ?UserInfoDto;
}
