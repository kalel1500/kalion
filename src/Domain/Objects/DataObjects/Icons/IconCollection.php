<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Icons;

use Thehouseofel\Kalion\Domain\Attributes\CollectionOf;
use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\AbstractCollectionDo;

#[CollectionOf(IconDo::class)]
final class IconCollection extends AbstractCollectionDo
{
}
