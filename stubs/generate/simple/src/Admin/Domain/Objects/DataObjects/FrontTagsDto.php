<?php

declare(strict_types=1);

namespace Src\Admin\Domain\Objects\DataObjects;

use Src\Shared\Domain\Objects\Entities\TagTypeEntity;
use Thehouseofel\Kalion\Domain\Objects\Collections\CollectionAny;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\ContractDataObject;

final class FrontTagsDto extends ContractDataObject
{
    public function __construct(
        public readonly ?TagTypeEntity $currentTagType,
        public readonly CollectionAny $pluckedTypes,
    )
    {
    }
}
