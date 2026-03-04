<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Layout;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie as CookieFacade;
use Symfony\Component\HttpFoundation\Cookie as HttpCookie;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\UserPreferencesDto;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\SidebarState;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\ThemeVo;

final class LayoutPreferencesCookieStore
{
    private string             $cookieName;
    private int                $cookieDuration;
    private string             $cookieVersion;
    private UserPreferencesDto $preferences;
    private ?HttpCookie        $cookie = null;

    public function __construct()
    {
        $this->cookieName     = config('kalion.cookie.name');
        $this->cookieDuration = config('kalion.cookie.duration');
        $this->cookieVersion  = config('kalion.cookie.version');
        $this->preferences    = new UserPreferencesDto(
            version               : config('kalion.cookie.version'),
            theme                 : ThemeVo::fromOr(config('kalion.layout.default_theme'), ThemeVo::getDefault()),
            sidebar_state         : SidebarState::fromOr(config('kalion.layout.default_sidebar_state'), SidebarState::getDefault()),
            sidebar_state_per_page: config('kalion.layout.sidebar_state_per_page'),
        );
    }

    public function cookie(): HttpCookie
    {
        return $this->cookie;
    }

    public function preferences(): UserPreferencesDto
    {
        return $this->preferences;
    }

    public function setPreferences(UserPreferencesDto $preferences): static
    {
        $this->preferences = $preferences;
        return $this;
    }

    public static function new(): static
    {
        return new static();
    }

    public static function readOrNew(): static
    {
        $service     = static::new();
        $preferences = UserPreferencesDto::fromJson(CookieFacade::get($service->cookieName));
        if (! is_null($preferences)) {
            $service->setPreferences($preferences);
        }
        return $service;
    }

    public function create(): static
    {
        // Crear la cookie usando CookieFacade::make
        $this->cookie = CookieFacade::make(
            name    : $this->cookieName,
            value   : $this->preferences->__toString(),
            minutes : $this->cookieDuration,
            path    : '/',
            secure  : config('session.secure'),
            httpOnly: false
        );
        return $this;
    }

    public function createIfNotExist(Request $request): static
    {
        // Verificar que la cookie no exista
        if (! $request->hasCookie($this->cookieName)) {
            // Crear la cookie usando CookieFacade::make
            return $this->create();
        }

        return $this;
    }

    public function queue(): static
    {
        if (! is_null($this->cookie)) {
            // Poner la cookie en la cola
            CookieFacade::queue($this->cookie);
        }
        return $this;
    }

    public function resetAndQueueIfExistInvalid(): ?self
    {
        if ($this->cookieVersion !== static::readOrNew()->preferences->version) {
            return static::new()->create()->queue();
        }
        return null;
    }
}
