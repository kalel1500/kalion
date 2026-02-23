<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Contracts\Repositories;

use Src\Shared\Domain\Objects\Entities\Collections\PostCollection;
use Src\Shared\Domain\Objects\Entities\PostEntity;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;

interface PostRepository
{
    public function all(): PostCollection;
    public function searchByTag(StringNullVo $tag_code): PostCollection;
    public function find(IdVo $id): PostEntity;
    public function findBySlug(StringVo $slug): PostEntity;
}
