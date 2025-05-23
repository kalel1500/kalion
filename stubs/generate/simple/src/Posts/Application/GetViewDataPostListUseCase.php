<?php

declare(strict_types=1);

namespace Src\Posts\Application;

use Src\Posts\Domain\Objects\DataObjects\ViewDataPostListDo;
use Src\Shared\Domain\Contracts\Repositories\PostRepositoryContract;
use Src\Shared\Domain\Contracts\Repositories\TagRepositoryContract;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelStringNull;

final readonly class GetViewDataPostListUseCase
{
    public function __construct(
        public PostRepositoryContract $repositoryPost,
        public TagRepositoryContract  $repositoryTag,
    )
    {
    }

    public function __invoke(?string $tag): ViewDataPostListDo
    {
        $tags  = $this->repositoryTag->all();
        $posts = $this->repositoryPost->searchByTag(ModelStringNull::new($tag));
        return ViewDataPostListDo::fromArray([
            $tags,
            $posts,
            $posts->count(),
            $tag,
        ]);
    }
}
