<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Responses;

use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\AbstractDataTransferObject;

class ResponseBroadcastDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly bool    $success,
        public readonly ?string $message,
    )
    {
    }
}
