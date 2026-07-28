<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure;

use Illuminate\Http\Request;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\EnvVo;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Config\Redirect\RedirectAfterLogin;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Config\Redirect\RedirectDefaultPath;

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

    public function redirectDefaultTo(Request $request): ?string
    {
        return app(RedirectDefaultPath::class)->redirectTo($request);
    }

    public function redirectAfterLoginTo(Request $request): ?string
    {
        return app(RedirectAfterLogin::class)->redirectTo($request);
    }
}
