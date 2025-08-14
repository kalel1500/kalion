<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
final class SubRelationDataDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly string|array|null $with,
        public readonly bool|string|null $isFull
    )
    {
    }
}
