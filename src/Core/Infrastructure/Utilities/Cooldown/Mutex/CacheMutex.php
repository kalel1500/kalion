<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cooldown\Mutex;

use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Support\Facades\Cache;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Cooldown\Contracts\Mutex;

class CacheMutex implements Mutex
{
    private const KEY_PREFIX = 'cooldown:mutex:';

    private readonly LockProvider $cache;

    public function __construct(?string $store = null) {
        $cache = Cache::store($store);

        if (! $cache->getStore() instanceof LockProvider) {
            throw new \RuntimeException(
                'UniqueExecution requires a cache store that supports atomic locks (e.g. redis). ' .
                'Check your "kalion.cooldown.cache_store" config.'
            );
        }

        $this->cache = $cache->getStore();
    }

    public function runExclusive(string $key, int $seconds, callable $callback): bool
    {
        $lock = $this->cache->lock(self::KEY_PREFIX . $key, $seconds);

        if (! $lock->get()) {
            return false;
        }

        try {
            $callback();
            return true;
        } finally {
            $lock->release(); // se ejecuta siempre, tanto si hay excepción como si no
        }
    }
}
