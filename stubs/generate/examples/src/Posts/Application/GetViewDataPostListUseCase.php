<?php

declare(strict_types=1);

namespace Src\Posts\Application;

use Src\Posts\Domain\Objects\DataObjects\ViewDataPostListDto;
use Src\Shared\Domain\Contracts\Repositories\PostRepository;
use Src\Shared\Domain\Contracts\Repositories\TagRepository;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelStringNull;

final readonly class GetViewDataPostListUseCase
{
    public function __construct(
        public PostRepository $repositoryPost,
        public TagRepository  $repositoryTag,
    )
    {
    }

    public function __invoke(?string $tag): ViewDataPostListDto
    {
        $tags  = $this->repositoryTag->all();
        $posts = $this->repositoryPost->searchByTag(ModelStringNull::new($tag));
        return ViewDataPostListDto::fromArray([
            $tags,
            $posts,
            $posts->count(),
            $tag,
        ]);
    }
}
