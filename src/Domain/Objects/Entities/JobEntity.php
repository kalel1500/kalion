<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelInt;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIntNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;

class JobEntity extends AbstractEntity
{
    public function __construct(
        public readonly ModelId|ModelIdNull $id,
        public readonly ModelString     $queue,
        public readonly ModelString     $payload,
        public readonly ModelInt        $attempts,
        public readonly ModelIntNull    $reserved_at,
        public readonly ModelInt        $available_at,
        public readonly ModelString     $created_at
    )
    {
    }
}
