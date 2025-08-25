<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Tests\Support\Repositories\Eloquent;

use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities\Collections\TagTypeCollection;
use Thehouseofel\Kalion\Tests\Support\Domain\Objects\Entities\TagTypeEntity;
use Thehouseofel\Kalion\Tests\Support\Models\TagType;

final class TagTypeRepository
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

    public function findByCode(ModelString $code): TagTypeEntity
    {
        $data = $this->model::query()->where('code', $code->value())->firstOrFail();
        return TagTypeEntity::fromArray($data->toArray());
    }
}
