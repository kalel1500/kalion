<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects;

final class SubRelationDataDo extends ContractDataObject
{
    public function __construct(
        public readonly string|array|null $with,
        public readonly bool|string|null $isFull
    )
    {
    }
}
