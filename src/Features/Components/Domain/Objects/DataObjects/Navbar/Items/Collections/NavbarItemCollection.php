<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Navbar\Items\Collections;

use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Abstracts\AbstractCollectionDto;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Attributes\CollectionOf;
use Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Navbar\Items\NavbarItemDto;

#[CollectionOf(NavbarItemDto::class)]
final class NavbarItemCollection extends AbstractCollectionDto
{
}
