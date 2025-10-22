<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\Collections;

use Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts\AbstractCollectionVo;
use Thehouseofel\Kalion\Domain\Objects\Collections\Attributes\CollectionOf;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IdVo;

#[CollectionOf(IdVo::class)]
final class CollectionIds extends AbstractCollectionVo
{
}
