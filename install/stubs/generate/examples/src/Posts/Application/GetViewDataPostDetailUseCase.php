<?php

declare(strict_types=1);

namespace Src\Posts\Application;

use Src\Shared\Domain\Contracts\Repositories\PostRepository;
use Src\Shared\Domain\Objects\Entities\PostEntity;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;

final readonly class GetViewDataPostDetailUseCase
{
    public function __construct(
        public PostRepository $repositoryPost,
    )
    {
    }

    public function __invoke(string $slug): PostEntity
    {
        return $this->repositoryPost->findBySlug(StringVo::from($slug));
    }
}
