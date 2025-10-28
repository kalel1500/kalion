<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Icons;

use Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts\AbstractCollectionDto;
use Thehouseofel\Kalion\Domain\Objects\Collections\Attributes\CollectionOf;

#[CollectionOf(IconDto::class)]
final class IconCollection extends AbstractCollectionDto
{
}
