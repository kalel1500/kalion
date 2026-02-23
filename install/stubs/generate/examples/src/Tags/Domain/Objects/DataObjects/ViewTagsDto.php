<?php

declare(strict_types=1);

namespace Src\Tags\Domain\Objects\DataObjects;

use Src\Shared\Domain\Objects\Entities\Collections\TagTypeCollection;
use Src\Shared\Domain\Objects\Entities\TagTypeEntity;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\AbstractDataTransferObject;

final class ViewTagsDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly ?TagTypeEntity $currentTagType,
        public readonly TagTypeCollection $tagTypes,
    )
    {
    }
}
