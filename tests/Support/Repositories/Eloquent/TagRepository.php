<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Thehouseofel\Kalion\Domain\Exceptions\Database\DuplicatedRecordException;
use Thehouseofel\Kalion\Domain\Exceptions\Database\EntityRelationException;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelStringNull;
use Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities\Collections\TagCollection;
use Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities\TagEntity;
use Thehouseofel\Kalion\Tests\Support\Models\Tag;

final class TagRepository
{
    private string $model;

    public function __construct()
    {
        $this->model = Tag::class;
    }

    public function all(): TagCollection
    {
        $data = $this->model::query()->get();
        return TagCollection::fromArray($data->toArray());
    }

    public function searchByType(ModelStringNull $typeCode): TagCollection
    {
        $data = $this->model::query()
            ->where(function (Builder $query) use ($typeCode) {
                if ($typeCode->isNotNull()) {
                    $query->whereHas('tagType', function (Builder $query2) use ($typeCode) {
                        $query2->where('code', $typeCode->value());
                    });
                }
            })
            ->get();
        return TagCollection::fromArray($data->toArray());
    }

    public function create(TagEntity $tag): void
    {
        $this->model::query()->create($tag->toArrayDb());
    }

    public function update(TagEntity $tag): void
    {
        $this->model::query()
            ->findOrFail($tag->id->value())
            ->update($tag->toArrayDb());
    }

    public function delete(ModelId $id): void
    {
        $this->model::query()
            ->findOrFail($id->value())
            ->delete();
    }

    public function throwIfExists(TagEntity $tag): void
    {
        $existNameOrCode = $this->model::query()
            ->newQuery()
            ->where(function (Builder $query) use ($tag) {
                if ($tag->id->isNotNull()) {
                    $query->where('id', '!=', $tag->id->value());
                }
            })
            ->where(function (Builder $query) use ($tag) {
                $query
                    ->where('name', $tag->name->value())
                    ->orWhere('code', $tag->code->value());
            })
            ->exists();

        if ($existNameOrCode) {
            $message = "A Tag with that name or code already exists";
            throw new DuplicatedRecordException($message);
        }
    }

    public function throwIfIsUsedByRelation(ModelId $id): void
    {
        $hasRelation = $this->model::query()
            ->findOrFail($id->value())
            ->posts()
            ->exists();
        if ($hasRelation) {
            throw EntityRelationException::cannotDeleteDueToRelation('Tag', 'Posts');
        }
    }
}
