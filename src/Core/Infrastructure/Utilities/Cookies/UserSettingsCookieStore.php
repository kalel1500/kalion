<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cookies;

use Illuminate\Support\Facades\Cookie as CookieFacade;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\SidebarState;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\ThemeVo;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cookies\Attributes\SkipCookieEncryption;

/**
 * @internal This class is intended for internal package usage only.
 */
#[SkipCookieEncryption]
class UserSettingsCookieStore implements CookieStore
{
    private string          $cookieName;
    private int             $cookieDuration;
    private string          $cookieVersion;
    private UserSettingsDto $object;
    private bool            $invalidCookie = false;

    public function __construct()
    {
        $this->cookieName     = config('kalion.cookies.user_settings.name');
        $this->cookieDuration = config('kalion.cookies.user_settings.duration');
        $this->cookieVersion  = config('kalion.cookies.user_settings.version');
        $this->object         = $this->getFromCookieOrDefault();
    }

    public function get(): UserSettingsDto
    {
        return $this->object;
    }

    public function set(mixed $payload): void
    {
        $object = ($payload instanceof UserSettingsDto)
            ? $payload
            : UserSettingsDto::fromJson(is_string($payload) ? $payload : null);

        if (! $object) {
            throw new \InvalidArgumentException('Invalid payload');
        }

        $this->object = $object;
        $this->writeCookie();
    }

    public function ensureValidCookie(): void
    {
        if (
            ! request()->hasCookie($this->cookieName) ||
            $this->invalidCookie ||
            $this->cookieVersion !== $this->object->version
        ) {
            $this->object = $this->defaultObject();
            $this->writeCookie();
        }
    }

    public function getCookieName(): string
    {
        return $this->cookieName;
    }

    protected function defaultObject(): UserSettingsDto
    {
        return new UserSettingsDto(
            version               : $this->cookieVersion,
            theme                 : ThemeVo::fromOr(config('kalion.layout.default_theme'), ThemeVo::getDefault()),
            sidebar_state         : SidebarState::fromOr(config('kalion.layout.default_sidebar_state'), SidebarState::getDefault()),
            sidebar_state_per_page: config('kalion.layout.sidebar_state_per_page'),
        );
    }

    protected function getFromCookieOrDefault(): UserSettingsDto
    {
        $cookie = CookieFacade::get($this->cookieName);

        try {
            $object = is_null($cookie) ? null : UserSettingsDto::fromJson((string)$cookie);
        } catch (\Throwable) {
            $this->invalidCookie = true;
            $object         = null;
        }

        return $object ?? $this->defaultObject();
    }

    protected function writeCookie(): void
    {
        CookieFacade::queue(CookieFacade::make(
            name    : $this->cookieName,
            value   : $this->object->__toString(),
            minutes : $this->cookieDuration,
            path    : '/',
            secure  : config('session.secure'),
            httpOnly: false,
        ));
    }
}
