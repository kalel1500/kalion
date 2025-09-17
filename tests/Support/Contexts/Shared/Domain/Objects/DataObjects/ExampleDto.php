<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Contexts\Shared\Domain\Objects\DataObjects;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\AbstractDataTransferObject;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters\CheckableProcessVo;

final class ExampleDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly string             $string1,
        public readonly string             $string2,
        public readonly int                $number,
        public readonly CheckableProcessVo $enum,
    )
    {
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'only_in_to_array' => 'text'
        ];
    }
}
