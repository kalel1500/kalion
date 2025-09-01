<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\Entities;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelBool;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelIdNull;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelStringNull;

class StatusEntity extends AbstractEntity
{
    public function __construct(
        public readonly ModelId|ModelIdNull $id,
        public readonly ModelString     $name,
        public readonly ModelBool       $finalized,
        public readonly ModelString     $code,
        public readonly ModelString     $type,
        public readonly ModelStringNull $icon
    )
    {
    }
}
