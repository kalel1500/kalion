<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelStringNull;
use Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities\Collections\PostCollection;
use Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities\PostEntity;
use Thehouseofel\Kalion\Tests\Support\Models\Post;

final class PostRepository
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
