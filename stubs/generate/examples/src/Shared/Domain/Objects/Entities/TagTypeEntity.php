<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Objects\Entities;

use Src\Shared\Domain\Objects\Entities\Collections\TagCollection;
use Thehouseofel\Kalion\Domain\Objects\Entities\AbstractEntity;
use Thehouseofel\Kalion\Domain\Objects\Entities\Attributes\RelationOf;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IdNullVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\IdVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\StringVo;

final class TagTypeEntity extends AbstractEntity
{
    public function __construct(
        public readonly IdVo|IdNullVo $id,
        public readonly StringVo      $name,
        public readonly StringVo      $code,
    )
    {
    }

    #[RelationOf(TagCollection::class)]
    public function tags(): TagCollection
    {
        return $this->getRelation();
    }
}
