<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Collections;

use Thehouseofel\Kalion\Domain\Attributes\CollectionOf;
use Thehouseofel\Kalion\Domain\Objects\Collections\Contracts\ContractCollectionDo;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\NavbarItemDo;

#[CollectionOf(NavbarItemDo::class)]
final class NavbarItemCollection extends ContractCollectionDo
{
}
