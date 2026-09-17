<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Collections;

use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Attributes\CollectionOf;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\TabulatorSortDto;

#[CollectionOf(TabulatorSortDto::class)]
class TabulatorSortCollection extends TabulatorDataCollection
{
}
