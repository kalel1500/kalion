<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Contracts\Repositories;

use Src\Shared\Domain\Objects\Entities\Collections\TagCollection;
use Src\Shared\Domain\Objects\Entities\TagEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelStringNull;

interface TagRepositoryContract
{
    public function all(): TagCollection;
    public function searchByType(ModelStringNull $typeCode): TagCollection;
    public function create(TagEntity $tag): void;
    public function update(TagEntity $tag): void;
    public function delete(ModelId $id): void;
    public function throwIfExists(TagEntity $tag): void;
    public function throwIfIsUsedByRelation(ModelId $id): void;
}
