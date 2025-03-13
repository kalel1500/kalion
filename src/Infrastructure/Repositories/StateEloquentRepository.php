<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Repositories;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Thehouseofel\Kalion\Domain\Contracts\Repositories\StateRepositoryContract;
use Thehouseofel\Kalion\Domain\Exceptions\Database\RecordNotFoundException;
use Thehouseofel\Kalion\Domain\Objects\Entities\Collections\StateCollection;
use Thehouseofel\Kalion\Domain\Objects\Entities\StateEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters\StatePluckFieldVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters\StatePluckKeyVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\EnumDynamicVo;
use Thehouseofel\Kalion\Infrastructure\Models\State;

class StateEloquentRepository implements StateRepositoryContract
{
    private string $eloquentModel;

    public function __construct()
    {
        $this->eloquentModel = State::class;
    }

    public function all(): StateCollection
    {
        $eloquentResult = $this->eloquentModel::query()->get();
        return StateCollection::fromEloquent($eloquentResult);
    }

    public function getDictionary(StatePluckFieldVo $field, StatePluckKeyVo $key): array
    {
        return $this->all()->pluck($field->value(), $key->value())->toArray();
    }

    public function getDictionaryByType(EnumDynamicVo $type, StatePluckFieldVo $field, StatePluckKeyVo $key): array
    {
        return $this->getByType($type)->pluck($field->value(), $key->value())->toArray();
    }

    public function getByType(EnumDynamicVo $type): StateCollection
    {
        $eloquentResult = $this->eloquentModel::query()
            ->where('type', $type->value())
            ->get();

        return StateCollection::fromEloquent($eloquentResult);
    }

    public function findByCode(EnumDynamicVo $code): StateEntity
    {
        try {
            $eloquentResult = $this->eloquentModel::query()
                ->where('code', $code->value())
                ->firstOrFail();
            return StateEntity::fromArray($eloquentResult->toArray());
        } catch (ModelNotFoundException $e) {
            throw new RecordNotFoundException($e->getMessage());
        }
    }
}
