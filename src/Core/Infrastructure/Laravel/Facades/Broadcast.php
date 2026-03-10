<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void dispatch(\Illuminate\Contracts\Broadcasting\ShouldBroadcast $errorMessage = null)
 *
 * @see \Thehouseofel\Kalion\Core\Infrastructure\Support\Broadcasting\BroadcastDispatcher
 */
final class Broadcast extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'kalion.broadcast';
    }
}
