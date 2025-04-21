<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Services;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Collections\NavbarItemCollection;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\UserInfoDo;

interface LayoutContract
{
    public function getMessageCounter(): int;
    public function getNavbarNotifications(): NavbarItemCollection;
    public function getUserInfo(): ?UserInfoDo;
}
