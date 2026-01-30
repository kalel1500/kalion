<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Services;

use Illuminate\Support\Facades\Cache;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\CheckableProcessVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\ProcessStatusKeysVo;

final class ProcessStatus
{
    /** --- GENERAL --- */

    private static function markAsDisabled(ProcessStatusKeysVo $key): void
    {
        Cache::forever($key->value, true);
    }

    private static function markAsEnabled(ProcessStatusKeysVo $key): void
    {
        Cache::forget($key->value);
    }

    private static function isDisabled(ProcessStatusKeysVo $key): bool
    {
        return (bool)Cache::get($key->value, false);
    }

    public static function update(CheckableProcessVo $processName, bool $active): void
    {
        $keyName = $processName->value . '_disabled';

        $key = ProcessStatusKeysVo::tryFrom($keyName);
        if (! $key) {
            return;
        }

        if ($active) {
            self::markAsEnabled($key);
        } else {
            self::markAsDisabled($key);
        }
    }


    /** --- QUEUE --- */

    public static function markQueueAsDisabled(): void
    {
        self::markAsDisabled(ProcessStatusKeysVo::queueDisabled);
    }

    public static function markQueueAsEnabled(): void
    {
        self::markAsEnabled(ProcessStatusKeysVo::queueDisabled);
    }

    public static function isQueueDisabled(): bool
    {
        return self::isDisabled(ProcessStatusKeysVo::queueDisabled);
    }


    /** --- Reverb --- */

    public static function markReverbAsDisabled(): void
    {
        self::markAsDisabled(ProcessStatusKeysVo::reverbDisabled);
    }

    public static function markReverbAsEnabled(): void
    {
        self::markAsEnabled(ProcessStatusKeysVo::reverbDisabled);
    }

    public static function isReverbDisabled(): bool
    {
        return self::isDisabled(ProcessStatusKeysVo::reverbDisabled);
    }
}
