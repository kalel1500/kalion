<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Shared\Infrastructure\Repositories\Eloquent;

use Thehouseofel\Kalion\Core\Domain\Objects\Entities\Collections\StatusCollection;
use Thehouseofel\Kalion\Core\Domain\Objects\Entities\StatusEntity;
use Thehouseofel\Kalion\Features\Shared\Domain\Contracts\Repositories\StatusRepository;
use Thehouseofel\Kalion\Features\Shared\Infrastructure\Models\Status;

class EloquentStatusRepository implements StatusRepository
{
    protected string $model = Status::class;

    public function all(): StatusCollection
    {
        $statuses = $this->model::query()->get();
        return StatusCollection::fromArray($statuses->toArray());
    }

    public function searchByType(string $type): StatusCollection
    {
        $statuses = $this->model::query()
            ->where('type', $type)
            ->get();

        return StatusCollection::fromArray($statuses->toArray());
    }

    public function findByCode(string $code): StatusEntity
    {
        $status = $this->model::query()
            ->where('code', $code)
            ->firstOrFail();
        return StatusEntity::fromArray($status->toArray());
    }
}
