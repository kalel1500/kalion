<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Layout;

use Illuminate\Support\Facades\Cookie as CookieFacade;
use Symfony\Component\HttpFoundation\Cookie as HttpCookie;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\UserPreferencesDto;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\SidebarState;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\ThemeVo;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
class LayoutPreferencesCookieStore implements PreferencesCookieStore
{
    private string             $cookieName;
    private int                $cookieDuration;
    private string             $cookieVersion;
    private UserPreferencesDto $preferences;
    private bool               $invalidCookie = false;

    public function __construct()
    {
        $this->cookieName     = config('kalion.cookie.name');
        $this->cookieDuration = config('kalion.cookie.duration');
        $this->cookieVersion  = config('kalion.cookie.version');
        $this->preferences    = $this->getPreferences();
    }

    public function get(): UserPreferencesDto
    {
        return $this->preferences;
    }

    public function set(string|UserPreferencesDto $preferences): void
    {
        $preferences = ($preferences instanceof UserPreferencesDto)
            ? $preferences
            : UserPreferencesDto::fromJson($preferences);

        if (!$preferences) {
            throw new \InvalidArgumentException('Invalid preferences payload');
        }

        $this->preferences = $preferences;
        $this->writeCookie();
    }

    public function ensureValidCookie(): void
    {
        if (
            ! request()->hasCookie($this->cookieName) ||
            $this->invalidCookie ||
            $this->cookieVersion !== $this->preferences->version
        ) {
            $this->preferences = $this->defaultPreferences();
            $this->writeCookie();
        }
    }


    protected function defaultPreferences(): UserPreferencesDto
    {
        return new UserPreferencesDto(
            version               : config('kalion.cookie.version'),
            theme                 : ThemeVo::fromOr(config('kalion.layout.default_theme'), ThemeVo::getDefault()),
            sidebar_state         : SidebarState::fromOr(config('kalion.layout.default_sidebar_state'), SidebarState::getDefault()),
            sidebar_state_per_page: config('kalion.layout.sidebar_state_per_page'),
        );
    }

    protected function getPreferences(): UserPreferencesDto
    {
        $cookie = CookieFacade::get($this->cookieName);

        try {
            $preferences = is_null($cookie) ? null : UserPreferencesDto::fromJson($cookie);
        } catch (\Throwable) {
            $this->invalidCookie = true;
            $preferences = null;
        }

        return $preferences ?? $this->defaultPreferences();
    }

    protected function writeCookie(): void
    {
        CookieFacade::queue(CookieFacade::make(
            name    : $this->cookieName,
            value   : $this->preferences->__toString(),
            minutes : $this->cookieDuration,
            path    : '/',
            secure  : config('session.secure'),
            httpOnly: false
        ));
    }
}
