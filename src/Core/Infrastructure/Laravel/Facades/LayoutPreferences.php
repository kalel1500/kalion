<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Layout\PreferencesCookieStore as PreferencesCookieStoreContract;

/**
 * @method static \Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\UserPreferencesDto get()
 * @method static void set(string|\Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\UserPreferencesDto $preferences)
 * @method static void ensureValidCookie()
 *
 * @see \Thehouseofel\Kalion\Core\Infrastructure\Support\Layout\LayoutPreferencesCookieStore
 */
class LayoutPreferences extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return PreferencesCookieStoreContract::class;
    }
}
