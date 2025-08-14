<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Collections;

use Thehouseofel\Kalion\Domain\Attributes\CollectionOf;
use Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts\AbstractCollectionDto;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\NavbarItemDto;

#[CollectionOf(NavbarItemDto::class)]
final class NavbarItemCollection extends AbstractCollectionDto
{
}
