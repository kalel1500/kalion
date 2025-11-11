<?php

declare(strict_types=1);

namespace Src\Tags\Application;

use Src\Shared\Domain\Contracts\Repositories\TagRepository;
use Src\Shared\Domain\Objects\Entities\Collections\TagCollection;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringNullVo;

final readonly class GetTagListUseCase
{
    public function __construct(
        private TagRepository $tagRepository,
    )
    {
    }

    public function __invoke(string $type = null): TagCollection
    {
        return $this->tagRepository->searchByType(StringNullVo::from($type));
    }
}
