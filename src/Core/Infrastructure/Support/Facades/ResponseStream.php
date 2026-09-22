<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Symfony\Component\HttpFoundation\StreamedResponse make(callable $callback, int $status = 200, array $headers = [])
 *
 * @see \Thehouseofel\Kalion\Core\Infrastructure\Utilities\Response\ResponseStreamFactory
 */
class ResponseStream extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'kalion.responseStream';
    }
}
