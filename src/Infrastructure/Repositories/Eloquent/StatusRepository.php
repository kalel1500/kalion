<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Repositories\Eloquent;

use Thehouseofel\Kalion\Domain\Contracts\Repositories\StatusRepositoryContract;
use Thehouseofel\Kalion\Domain\Objects\Entities\Collections\StatusCollection;
use Thehouseofel\Kalion\Domain\Objects\Entities\StatusEntity;
use Thehouseofel\Kalion\Infrastructure\Models\Status;

class StatusRepository implements StatusRepositoryContract
{
    protected string $model = Status::class;

    public function all(): StatusCollection
    {
        $eloquentResult = $this->model::query()->get();
        return StatusCollection::fromEloquent($eloquentResult);
    }

    public function searchByType(string $type): StatusCollection
    {
        $eloquentResult = $this->model::query()
            ->where('type', $type)
            ->get();

        return StatusCollection::fromEloquent($eloquentResult);
    }

    public function findByCode(string $code): StatusEntity
    {
        $eloquentResult = $this->model::query()
            ->where('code', $code)
            ->firstOrFail();
        return StatusEntity::fromArray($eloquentResult->toArray());
    }
}
