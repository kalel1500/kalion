<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Contracts\Repositories;

use Src\Shared\Domain\Objects\Entities\Collections\CommentCollection;
use Src\Shared\Domain\Objects\Entities\CommentEntity;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdVo;

interface CommentRepository
{
    public function searchByPost(IdVo $post_id): CommentCollection;
    public function create(CommentEntity $comment): void;
    public function update(CommentEntity $comment): void;
}
