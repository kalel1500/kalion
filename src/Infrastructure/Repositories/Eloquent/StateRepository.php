<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Repositories\Eloquent;

use Thehouseofel\Kalion\Domain\Contracts\Repositories\StateRepositoryContract;
use Thehouseofel\Kalion\Domain\Objects\Entities\Collections\StateCollection;
use Thehouseofel\Kalion\Domain\Objects\Entities\StateEntity;
use Thehouseofel\Kalion\Infrastructure\Models\State;

class StateRepository implements StateRepositoryContract
{
    protected string $eloquentModel;

    public function __construct()
    {
        $this->eloquentModel = State::class;
    }

    public function all(): StateCollection
    {
        $eloquentResult = $this->eloquentModel::query()->get();
        return StateCollection::fromEloquent($eloquentResult);
    }

    public function searchByType(string $type): StateCollection
    {
        $eloquentResult = $this->eloquentModel::query()
            ->where('type', $type)
            ->get();

        return StateCollection::fromEloquent($eloquentResult);
    }

    public function findByCode(string $code): StateEntity
    {
        $eloquentResult = $this->eloquentModel::query()
            ->where('code', $code)
            ->firstOrFail();
        return StateEntity::fromArray($eloquentResult->toArray());
    }
}
