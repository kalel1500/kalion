<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Services\Contracts;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Collections\NavbarItemCollection;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\UserInfoDto;

interface LayoutData
{
    public function getMessageCounter(): int;
    public function getNavbarNotifications(): NavbarItemCollection;
    public function getUserInfo(): ?UserInfoDto;
}
