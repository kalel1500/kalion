<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Responses;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\AbstractDataTransferObject;

final class ResponseBroadcastDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly bool    $success,
        public readonly ?string $message,
    )
    {
    }
}
