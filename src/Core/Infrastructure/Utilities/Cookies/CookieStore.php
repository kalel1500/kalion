<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cookies;

interface CookieStore
{
    public function get(): mixed;

    public function set(mixed $payload): void;

    public function ensureValidCookie(): void;

    public function getCookieName(): string;
}
