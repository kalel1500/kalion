<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Repositories\Eloquent;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities\Collections\CommentCollection;
use Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities\CommentEntity;
use Thehouseofel\Kalion\Tests\Support\Models\Comment;

final class CommentRepository
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
