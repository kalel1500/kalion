<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Layout;

use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\UserPreferencesDto;

interface PreferencesCookieStore
{
    public function get(): UserPreferencesDto;

    public function set(string|UserPreferencesDto $preferences): void;

    public function ensureValidCookie(): void;
}
