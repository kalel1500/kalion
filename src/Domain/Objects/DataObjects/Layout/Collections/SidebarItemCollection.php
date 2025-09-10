<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Collections;

use Thehouseofel\Kalion\Domain\Objects\Collections\Attributes\CollectionOf;
use Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts\AbstractCollectionDto;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\SidebarItemDto;

#[CollectionOf(SidebarItemDto::class)]
final class SidebarItemCollection extends AbstractCollectionDto
{
}
