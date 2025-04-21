<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Services\Repository;

use Src\Shared\Domain\Contracts\Repositories\TagTypeRepositoryContract;
use Src\Shared\Domain\Objects\Entities\TagTypeEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelStringNull;

final readonly class TagTypeService
{
    public function __construct(
        private TagTypeRepositoryContract $tagTypeRepository,
    )
    {
    }

    public function findByCode(ModelStringNull $code): ?TagTypeEntity
    {
        if ($code->isNull()) return null;

        $code = $code->toNotNull();
        return $this->tagTypeRepository->findByCode($code);
    }
}
