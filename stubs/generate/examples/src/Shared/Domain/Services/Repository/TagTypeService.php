<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Services\Repository;

use Src\Shared\Domain\Contracts\Repositories\TagTypeRepository;
use Src\Shared\Domain\Objects\Entities\TagTypeEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\StringNullVo;

final readonly class TagTypeService
{
    public function __construct(
        private TagTypeRepository $tagTypeRepository,
    )
    {
    }

    public function findByCode(StringNullVo $code): ?TagTypeEntity
    {
        if ($code->isNull()) return null;

        $code = $code->toNotNull();
        return $this->tagTypeRepository->findByCode($code);
    }
}
