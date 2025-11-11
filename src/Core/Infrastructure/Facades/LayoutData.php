<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Layout\Collections\NavbarItemCollection;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Layout\UserInfoDto;
use Thehouseofel\Kalion\Core\Domain\Services\Contracts\LayoutData as LayoutDataContract;

/**
 * @method static int getMessageCounter()
 * @method static NavbarItemCollection getNavbarNotifications()
 * @method static UserInfoDto getUserInfo()
 *
 * @see \Thehouseofel\Kalion\Core\Domain\Services\BaseLayoutData
 */
final class LayoutData extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return LayoutDataContract::class;
    }
}
