<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Icons;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\ContractDataObject;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\BoolVo;

final class ViewIconsDo extends ContractDataObject
{
    public function __construct(
        public readonly IconCollection $icons,
        public readonly BoolVo         $show_name_short
    )
    {
    }

    protected static function createFromArray(array $data): static
    {
        return new static(
            IconCollection::fromArray($data['icons'] ?? null),
            BoolVo::new($data['show_name_short'] ?? null)
        );
    }
}
