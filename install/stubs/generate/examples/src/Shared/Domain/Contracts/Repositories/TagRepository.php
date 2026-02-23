<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Contracts\Repositories;

use Src\Shared\Domain\Objects\Entities\Collections\TagCollection;
use Src\Shared\Domain\Objects\Entities\TagEntity;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringNullVo;

interface TagRepository
{
    public function all(): TagCollection;
    public function searchByType(StringNullVo $typeCode): TagCollection;
    public function create(TagEntity $tag): void;
    public function update(TagEntity $tag): void;
    public function delete(IdVo $id): void;
    public function throwIfExists(TagEntity $tag): void;
    public function throwIfIsUsedByRelation(IdVo $id): void;
}
