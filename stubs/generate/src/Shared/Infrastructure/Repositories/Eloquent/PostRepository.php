<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Src\Shared\Domain\Contracts\Repositories\PostRepositoryContract;
use Src\Shared\Domain\Objects\Entities\Collections\PostCollection;
use Src\Shared\Domain\Objects\Entities\PostEntity;
use Src\Shared\Infrastructure\Models\Post;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelStringNull;

final class PostRepository implements PostRepositoryContract
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

    public function searchByTag(ModelStringNull $tag_code): PostCollection
    {
        $data = $this->model::query()
            ->with('tags')
            ->where(function (Builder $query) use ($tag_code) {
                if ($tag_code->isNotNull()) {
                    $query->whereHas('tags', function (Builder $query) use ($tag_code) {
                        $query->where('code', 'LIKE', "%{$tag_code->value()}%");
                    });
                }
            })
            ->get();
        return PostCollection::fromArray($data->toArray(), ['tags']);
    }

    public function find(ModelId $id): PostEntity
    {
        $data = $this->model::query()->with('comments')->findOrFail($id);
        return PostEntity::fromArray($data->toArray(), ['comments']);
    }

    public function findBySlug(ModelString $slug): PostEntity
    {
        $data = $this->model::query()
            ->with('comments')
            ->where('slug', $slug->value())
            ->firstOrFail();
        return PostEntity::fromArray($data->toArray(), ['comments']);
    }
}
