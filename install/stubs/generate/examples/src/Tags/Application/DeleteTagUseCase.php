<?php

declare(strict_types=1);

namespace Src\Tags\Application;

use Src\Shared\Domain\Contracts\Repositories\TagRepository;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdVo;

final readonly class DeleteTagUseCase
{
    public function __construct(
        private TagRepository $tagRepository,
    )
    {
    }

    public function __invoke(int $id): void
    {
        $id = IdVo::from($id);
        $this->tagRepository->throwIfIsUsedByRelation($id);
        $this->tagRepository->delete($id);
    }
}
