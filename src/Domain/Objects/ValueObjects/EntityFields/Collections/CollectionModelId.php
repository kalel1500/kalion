<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\Collections;

use Thehouseofel\Kalion\Domain\Attributes\CollectionOf;
use Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts\AbstractCollectionVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;

#[CollectionOf(ModelId::class)]
final class CollectionModelId extends AbstractCollectionVo
{
}
