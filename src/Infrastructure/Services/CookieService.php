<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie as CookieFacade;
use Symfony\Component\HttpFoundation\Cookie;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\CookiePreferencesDo;

final class CookieService
{
    private string              $cookieName;
    private int                 $cookieDuration;
    private string              $cookieVersion;
    private CookiePreferencesDo $preferences;
    private ?Cookie             $cookie = null;

    public function __construct()
    {
        $this->cookieName     = config('kalion.cookie.name');
        $this->cookieDuration = config('kalion.cookie.duration');
        $this->cookieVersion  = config('kalion.cookie.version');
        $this->preferences    = CookiePreferencesDo::fromArray([
            'version'                => config('kalion.cookie.version'),
            'theme'                  => config('kalion.layout.theme'),
            'sidebar_collapsed'      => config('kalion.layout.sidebar_collapsed'),
            'sidebar_state_per_page' => config('kalion.layout.sidebar_state_per_page'),
        ]);
    }

    public function cookie(): Cookie
    {
        return $this->cookie;
    }

    public function preferences(): CookiePreferencesDo
    {
        return $this->preferences;
    }

    public function setPreferences(CookiePreferencesDo $preferences): static
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
        $preferences = CookiePreferencesDo::fromJson(CookieFacade::get($service->cookieName));
        if (!is_null($preferences)) {
            $service->setPreferences($preferences);
        }
        return $service;
    }

    public function create(): static
    {
        // Crear la cookie usando CookieFacade::make
        $this->cookie = CookieFacade::make(
            $this->cookieName,
            $this->preferences->__toString(),
            $this->cookieDuration,
            '/',
            null,
            true,
            false
        );
        return $this;
    }

    public function createIfNotExist(Request $request): static
    {
        // Verificar que la cookie no exista
        if (!$request->hasCookie($this->cookieName)) {
            // Crear la cookie usando CookieFacade::make
            return $this->create();
        }

        return $this;
    }

    public function queue(): static
    {
        if (!is_null($this->cookie)) {
            // Poner la cookie en la cola
            CookieFacade::queue($this->cookie);
        }
        return $this;
    }

    public function resetAndQueueIfExistInvalid(): ?self
    {
        if ($this->cookieVersion !== static::readOrNew()->preferences->version()) {
            return static::new()->create()->queue();
        }
        return null;
    }
}
