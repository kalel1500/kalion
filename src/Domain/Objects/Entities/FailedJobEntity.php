<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;

class FailedJobEntity extends AbstractEntity
{
    public function __construct(
        public readonly ModelId|ModelIdNull $id,
        public readonly ModelString     $uuid,
        public readonly ModelString     $connection,
        public readonly ModelString     $queue,
        public readonly ModelString     $payload,
        public readonly ModelString     $exception,
        public readonly ModelString     $failed_at
    )
    {
    }
}
