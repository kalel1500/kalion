<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Icons;

use Thehouseofel\Kalion\Domain\Attributes\CollectionOf;
use Thehouseofel\Kalion\Domain\Objects\Collections\Abstracts\AbstractCollectionDto;

#[CollectionOf(IconDto::class)]
final class IconCollection extends AbstractCollectionDto
{
}
