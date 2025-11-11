<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Objects\DataObjects;

use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Abstracts\AbstractCollectionDto;
use Thehouseofel\Kalion\Core\Domain\Objects\Collections\Attributes\CollectionOf;

#[CollectionOf(ExampleDto::class)]
final class ExampleDtoCollection extends AbstractCollectionDto
{
}
