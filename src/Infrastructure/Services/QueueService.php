<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services;

use Illuminate\Support\Facades\Artisan;
use Thehouseofel\Kalion\Domain\Exceptions\ServiceException;
use Throwable;

final class QueueService
{
    /**
     * @throws Throwable
     */
    public static function check(string $failMessage): void
    {
        $exitCode = Artisan::call('kalion:service-check');
        $active = ($exitCode !== 0 && !str_contains(Artisan::output(), 'inactivo'));
        if (!$active) {
            cache(['service_queue' => 'inactive'], (10*60));
            throw new ServiceException($failMessage);
        }
        cache(['service_queue' => 'active'], (10*60));
    }
}
