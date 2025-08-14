<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Collections;

use Thehouseofel\Kalion\Domain\Attributes\CollectionOf;
use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\AbstractCollectionDo;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\SidebarItemDo;

#[CollectionOf(SidebarItemDo::class)]
final class SidebarItemCollection extends AbstractCollectionDo
{
}
