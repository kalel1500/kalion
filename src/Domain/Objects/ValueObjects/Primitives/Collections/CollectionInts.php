<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Collections;

use Thehouseofel\Kalion\Domain\Objects\Collections\Attributes\CollectionOf;
use Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts\AbstractCollectionVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IntVo;

#[CollectionOf(IntVo::class)]
final class CollectionInts extends AbstractCollectionVo
{
}
