<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Internal;

use ReflectionClass;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cookies\Attributes\SkipCookieEncryption;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cookies\CookieStore;

/**
 * @internal This class is intended for internal package usage only.
 */
class ConfigParser
{
    /**
     * @return string[]
     */
    public function getActiveCookieStoreClasses(): array
    {
        return collect((array) config('kalion.cookies', []))
            ->filter(static fn($cookie): bool => ! empty($cookie['active']))
            ->pluck('store')
            ->filter(static fn($storeClass): bool =>
                is_string($storeClass)
                && $storeClass !== ''
                && class_exists($storeClass)
                && is_subclass_of($storeClass, CookieStore::class)
            )
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return string[]
     */
    public function getEnsureValidCookiesMiddlewareGroups(): array
    {
        return collect((array) config('kalion.cookies', []))
            ->filter(static fn($cookie): bool => ! empty($cookie['active']))
            ->pluck('middleware_groups')
            ->filter(static fn($groups): bool => is_string($groups) && $groups !== '')
            ->flatMap(static fn(string $groups): array => array_map('trim', explode(',', $groups)))
            ->filter(static fn(string $group): bool => $group !== '')
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @throws \ReflectionException
     */
    public function getCookiesToSkipEncryption(): array
    {
        if (empty(config('app.key'))) {
            return [];
        }

        $cookieNames = [];

        foreach ($this->getActiveCookieStoreClasses() as $storeClass) {

            $attributes = (new ReflectionClass($storeClass))->getAttributes(SkipCookieEncryption::class);
            if (empty($attributes)) {
                continue;
            }

            /** @var CookieStore $store */
            $store = app($storeClass);
            $cookieNames[] = $store->getCookieName();
        }

        return array_values(array_unique(array_filter($cookieNames, static fn($name): bool => is_string($name) && $name !== '')));
    }
}

