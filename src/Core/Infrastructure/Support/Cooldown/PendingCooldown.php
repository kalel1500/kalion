<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Cooldown;

use Carbon\CarbonImmutable;
use DateTimeInterface;
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
        private readonly string $key,
        private CooldownStore   $store,
        private readonly Mutex  $mutex,
    )
    {
    }

    /**
     * @param int|DateTimeInterface $time DateTimeInterface instance or seconds
     */
    public function every(int|DateTimeInterface $time): static
    {
        $this->everySeconds = $time instanceof DateTimeInterface ? CarbonImmutable::now()->diffInSeconds($time, true) : $time;
        return $this;
    }

    /**
     * @param int|DateTimeInterface $time DateTimeInterface instance or seconds
     */
    public function expiresIn(int|DateTimeInterface $time): static
    {
        $this->mutexSeconds = $time instanceof DateTimeInterface ? CarbonImmutable::now()->diffInSeconds($time, true) : $time;
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

    public function withStore(CooldownStore $store): static
    {
        $this->store = $store;
        return $this;
    }

    public function run(callable $callback): mixed
    {
        $lastExecutedAt = $this->store->getLastExecutedAt();

        // skipWhen: deshabilitado por config
        if ($this->skipWhen) {
            return $this->resolveSkipped($lastExecutedAt);
        }

        // Throttle check (ignorado si forceWhen)
        if (! $this->forceWhen && $this->isCoolingDown($lastExecutedAt)) {
            return $this->resolveSkipped($lastExecutedAt);
        }

        // Mutex atómico
        $result            = null;
        $passedDoubleCheck = false;

        $acquired = $this->mutex->runExclusive(
            key     : $this->key,
            seconds : $this->mutexSeconds,
            callback: function () use ($callback, &$result, &$passedDoubleCheck) {
                // Double-check in lock
                $lastExecutedAt = $this->store->getLastExecutedAt();
                if (! $this->forceWhen && $this->isCoolingDown($lastExecutedAt)) {
                    return;
                }
                $passedDoubleCheck = true;

                $context = new CooldownContext();
                $result = $callback($lastExecutedAt, $context);
                if ($context->shouldUpdateLastExecutedAt()) {
                    $this->store->setLastExecutedAt(CarbonImmutable::now());
                }
            }
        );

        // post-mutex
        $lastExecutedAt = $this->store->getLastExecutedAt();

        // No consiguió el mutex → otro proceso en curso
        if (! $acquired) {
            return $this->resolveLocked($lastExecutedAt);
        }

        // Consiguió el mutex pero el double-check lo detuvo
        if (! $passedDoubleCheck) {
            return $this->resolveSkipped($lastExecutedAt);
        }

        return $result;
    }

    private function resolveSkipped(?CarbonImmutable $lastExecutedAt): mixed
    {
        return $this->onSkipped
            ? ($this->onSkipped)($lastExecutedAt)
            : $lastExecutedAt;
    }

    private function resolveLocked(?CarbonImmutable $lastExecutedAt): mixed
    {
        return $this->onLocked
            ? ($this->onLocked)($lastExecutedAt)
            : $lastExecutedAt;
    }

    private function isCoolingDown(?CarbonImmutable $lastExecutedAt): bool
    {
        return $lastExecutedAt?->clone()->addSeconds($this->everySeconds)->isFuture() ?? false;
    }
}
