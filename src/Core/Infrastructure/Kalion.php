<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure;

use Illuminate\Http\Request;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\EnvVo;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Config\Redirect\RedirectGuests;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Config\Redirect\RedirectUsers;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cookies\UserSettingsCookieStore;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Internal\PackageAssets;

class Kalion
{
    protected ?EnvVo $environment = null;

    public function environment(): EnvVo
    {
        return $this->environment ??= EnvVo::from(config('app.env'));
    }

    public function isProd(): bool
    {
        return $this->environment()->isProd();
    }

    public function isLocal(): bool
    {
        return $this->environment()->isLocal();
    }

    public function isPre(): bool
    {
        return $this->environment()->isPre();
    }

    public function isTesting(): bool
    {
        return $this->environment()->isTesting();
    }

    public function renderCss(): string
    {
        return PackageAssets::css();
    }

    public function renderJs(): string
    {
        return PackageAssets::js();
    }

    public function redirectGuestsTo(Request $request): ?string
    {
        return app(RedirectGuests::class)->redirectTo($request);
    }

    public function redirectUsersTo(Request $request): ?string
    {
        return app(RedirectUsers::class)->redirectTo($request);
    }

    public function userSettings(): UserSettingsCookieStore
    {
        return app(UserSettingsCookieStore::class);
    }
}
