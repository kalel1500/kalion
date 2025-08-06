<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Src\Shared\Domain\Contracts\Repositories\TagRepositoryContract;
use Src\Shared\Domain\Objects\Entities\Collections\TagCollection;
use Src\Shared\Domain\Objects\Entities\TagEntity;
use Src\Shared\Infrastructure\Models\Tag;
use Thehouseofel\Kalion\Domain\Exceptions\Database\DuplicatedRecordException;
use Thehouseofel\Kalion\Domain\Exceptions\Database\EntityRelationException;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelStringNull;

final class TagRepository implements TagRepositoryContract
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
            $message = __('k::database.model_already_exist_with_fields', ['model' => 'Tag', 'fields' => concat_fields_with(['name', 'code'], 'or')]);
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
