<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects;

class ResultDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly bool    $success,
        public readonly ?string $message,
    )
    {
    }
}
