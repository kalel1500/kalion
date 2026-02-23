<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Src\Shared\Domain\Contracts\Repositories\PostRepository;
use Src\Shared\Domain\Objects\Entities\Collections\PostCollection;
use Src\Shared\Domain\Objects\Entities\PostEntity;
use Src\Shared\Infrastructure\Models\Post;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\IdVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringNullVo;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;

final class EloquentPostRepository implements PostRepository
{
    private string $model;

    public function __construct()
    {
        $this->model = Post::class;
    }

    public function all(): PostCollection
    {
        $data = $this->model::query()->with('comments')->get();
        return PostCollection::fromArray($data->toArray(), ['comments']);
    }

    public function searchByTag(StringNullVo $tag_code): PostCollection
    {
        $data = $this->model::query()
            ->with('tags')
            ->where(function (Builder $query) use ($tag_code) {
                if ($tag_code->isNotNull()) {
                    $query->whereHas('tags', function (Builder $query) use ($tag_code) {
                        $query->where('code', 'LIKE', "%{$tag_code->value}%");
                    });
                }
            })
            ->get();
        return PostCollection::fromArray($data->toArray(), ['tags']);
    }

    public function find(IdVo $id): PostEntity
    {
        $data = $this->model::query()->with('comments')->findOrFail($id);
        return PostEntity::fromArray($data->toArray(), ['comments']);
    }

    public function findBySlug(StringVo $slug): PostEntity
    {
        $data = $this->model::query()
            ->with('comments')
            ->where('slug', $slug->value)
            ->firstOrFail();
        return PostEntity::fromArray($data->toArray(), ['comments']);
    }
}
