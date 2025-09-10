<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Repositories\Eloquent;

use Src\Shared\Domain\Contracts\Repositories\CommentRepository;
use Src\Shared\Domain\Objects\Entities\Collections\CommentCollection;
use Src\Shared\Domain\Objects\Entities\CommentEntity;
use Src\Shared\Infrastructure\Models\Comment;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;

final class EloquentCommentRepository implements CommentRepository
{
    private string $model;

    public function __construct()
    {
        $this->model = Comment::class;
    }

    public function searchByPost(ModelId $post_id): CommentCollection
    {
        $data = $this->model::query()
            ->with('post')
            ->where('post_id', $post_id->value())
            ->get();
        return CommentCollection::fromArray($data->toArray(), ['post']);
    }

    public function create(CommentEntity $comment): void
    {
        $this->model::query()->create($comment->toArrayDb());
    }

    public function update(CommentEntity $comment): void
    {
        $this->model->newQuery()
            ->findOrFail($comment->id->value())
            ->update($comment->toArrayDb());
    }
}
