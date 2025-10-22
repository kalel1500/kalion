<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Repositories\Eloquent;

use Src\Shared\Domain\Contracts\Repositories\TagTypeRepository;
use Src\Shared\Domain\Objects\Entities\Collections\TagTypeCollection;
use Src\Shared\Domain\Objects\Entities\TagTypeEntity;
use Src\Shared\Infrastructure\Models\TagType;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\StringVo;

final class EloquentTagTypeRepository implements TagTypeRepository
{
    private string $model;

    public function __construct()
    {
        $this->model = TagType::class;
    }

    public function all(): TagTypeCollection
    {
        $data = $this->model::query()->get();
        return TagTypeCollection::fromArray($data->toArray());
    }

    public function findByCode(StringVo $code): TagTypeEntity
    {
        $data = $this->model::query()->where('code', $code->value())->firstOrFail();
        return TagTypeEntity::fromArray($data->toArray());
    }
}
