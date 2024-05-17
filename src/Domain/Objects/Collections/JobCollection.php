<?php

declare(strict_types=1);

namespace Thehouseofel\Hexagonal\Domain\Objects\Collections;

use Thehouseofel\Hexagonal\Domain\Objects\Collections\Contracts\ContractCollectionEntity;
use Thehouseofel\Hexagonal\Domain\Objects\Entities\JobEntity;

final class JobCollection extends ContractCollectionEntity
{
    public const ENTITY = JobEntity::class;

    public function __construct(JobEntity ...$items)
    {
        $this->items = $items;
    }
}
