<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Icons;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\AbstractDataTransferObject;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\BoolVo;

final class ViewIconsDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly IconCollection $icons,
        public readonly BoolVo         $show_name_short
    )
    {
    }

    protected static function make(array $data): static
    {
        return new static(
            IconCollection::fromArray($data['icons'] ?? null),
            BoolVo::new($data['show_name_short'] ?? null)
        );
    }
}
