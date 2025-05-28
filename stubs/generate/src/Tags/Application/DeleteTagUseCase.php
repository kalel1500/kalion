<?php

declare(strict_types=1);

namespace Src\Tags\Application;

use Src\Shared\Domain\Contracts\Repositories\TagRepositoryContract;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;

final readonly class DeleteTagUseCase
{
    public function __construct(
        private TagRepositoryContract $tagRepository,
    )
    {
    }

    public function __invoke(int $id): void
    {
        $id = ModelId::new($id);
        $this->tagRepository->throwIfIsUsedByRelation($id);
        $this->tagRepository->delete($id);
    }
}
