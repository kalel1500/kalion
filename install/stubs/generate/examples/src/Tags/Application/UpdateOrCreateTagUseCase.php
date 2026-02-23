<?php

declare(strict_types=1);

namespace Src\Tags\Application;

use Src\Shared\Domain\Contracts\Repositories\TagRepository;
use Src\Shared\Domain\Objects\Entities\TagEntity;

final readonly class UpdateOrCreateTagUseCase
{
    public function __construct(
        private TagRepository $tagRepository,
    )
    {
    }

    public function __invoke(array $params): void
    {
        $tag = TagEntity::fromArray($params);

        $this->tagRepository->throwIfExists($tag);

        if ($tag->id->isNull()) {
            $this->tagRepository->create($tag);
        } else {
            $this->tagRepository->update($tag);
        }
    }
}
