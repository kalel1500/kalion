<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Collections\NavbarItemCollection;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\UserInfoDo;

/**
 * @method static int getMessageCounter()
 * @method static NavbarItemCollection getNavbarNotifications()
 * @method static UserInfoDo getUserInfo()
 *
 * @see \Thehouseofel\Kalion\Domain\Services\RepositoryServices\LayoutService
 */
final class LayoutService extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'layoutService';
    }
}
