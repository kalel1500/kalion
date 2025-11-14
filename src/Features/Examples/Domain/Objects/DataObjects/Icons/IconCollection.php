<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Examples\Domain\Objects\DataObjects\Icons;

use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Abstracts\AbstractCollectionDto;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Attributes\CollectionOf;

#[CollectionOf(IconDto::class)]
final class IconCollection extends AbstractCollectionDto
{
}
