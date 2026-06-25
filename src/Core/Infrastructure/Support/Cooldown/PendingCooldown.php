<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown;

use DateTimeInterface;
use Carbon\Carbon;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown\Contracts\CooldownStore;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown\Contracts\Mutex;

class PendingCooldown
{
    private int   $everySeconds = 3600; // 1h
    private int   $mutexSeconds = 300; // 5m
    private bool  $skipWhen     = false;
    private bool  $forceWhen    = false;
    private mixed $onSkipped    = null;
    private mixed $onLocked     = null;

    public function __construct(
        private readonly string        $key,
        private readonly CooldownStore $store,
        private readonly Mutex         $mutex,
    )
    {
    }

    /**
     * @param int|DateTimeInterface $time DateTimeInterface instance or seconds
     */
    public function every(int|DateTimeInterface $time): static
    {
        $this->everySeconds = $time instanceof DateTimeInterface ? Carbon::now()->diffInSeconds($time, true) : $time;
        return $this;
    }

    /**
     * @param int|DateTimeInterface $time DateTimeInterface instance or seconds
     */
    public function expiresIn(int|DateTimeInterface $time): static
    {
        $this->mutexSeconds = $time instanceof DateTimeInterface ? Carbon::now()->diffInSeconds($time, true) : $time;
        return $this;
    }

    /**
     * Si es true, el proceso siempre se considera "skipped" sin ejecutar nada.
     * Útil para deshabilitar el proceso via config.
     */
    public function skipWhen(bool $condition): static
    {
        $this->skipWhen = $condition;
        return $this;
    }

    /**
     * Si es true, ignora el TTL y fuerza la ejecución.
     * El mutex sigue activo para evitar ejecuciones concurrentes.
     */
    public function forceWhen(bool $condition): static
    {
        $this->forceWhen = $condition;
        return $this;
    }

    public function onSkipped(callable $callback): static
    {
        $this->onSkipped = $callback;
        return $this;
    }

    public function onLocked(callable $callback): static
    {
        $this->onLocked = $callback;
        return $this;
    }

    public function run(callable $callback): mixed
    {
        $lastExecutedAt = $this->store->getLastExecutedAt($this->key);

        // skipWhen: deshabilitado por config
        if ($this->skipWhen) {
            return $this->resolveSkipped($lastExecutedAt);
        }

        // Throttle check (ignorado si forceWhen)
        if (! $this->forceWhen && $this->isCoolingDown($lastExecutedAt)) {
            return $this->resolveSkipped($lastExecutedAt);
        }

        // Mutex atómico
        $result = null;
        $didRun = false;

        $acquired = $this->mutex->runExclusive(
            key     : $this->key,
            seconds : $this->mutexSeconds,
            callback: function () use ($callback, &$result, &$didRun) {
                // Double-check in lock
                $lastExecutedAt = $this->store->getLastExecutedAt($this->key);
                if (! $this->forceWhen && $this->isCoolingDown($lastExecutedAt)) {
                    return;
                }

                $result = $callback($lastExecutedAt);
                $didRun = true;

                $this->store->setLastExecutedAt(
                    key : $this->key,
                    time: Carbon::now(),
                );
            }
        );

        // post-mutex
        $lastExecutedAt = $this->store->getLastExecutedAt($this->key);

        // No consiguió el mutex → otro proceso en curso
        if (! $acquired) {
            return $this->resolveLocked($lastExecutedAt);
        }

        // Consiguió el mutex pero el double-check lo detuvo
        if (! $didRun) {
            return $this->resolveSkipped($lastExecutedAt);
        }

        return $result;
    }

    private function resolveSkipped(?Carbon $lastExecutedAt): mixed
    {
        return $this->onSkipped
            ? ($this->onSkipped)($lastExecutedAt)
            : $lastExecutedAt;
    }

    private function resolveLocked(?Carbon $lastExecutedAt): mixed
    {
        return $this->onLocked
            ? ($this->onLocked)($lastExecutedAt)
            : $lastExecutedAt;
    }

    private function isCoolingDown(?Carbon $lastExecutedAt): bool
    {
        return $lastExecutedAt?->clone()->addSeconds($this->everySeconds)->isFuture() ?? false;
    }
}
