<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Repositories;

use Thehouseofel\Kalion\Domain\Objects\Entities\Collections\StateCollection;
use Thehouseofel\Kalion\Domain\Objects\Entities\StateEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters\StatePluckFieldVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters\StatePluckKeyVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\EnumDynamicVo;

interface StateRepositoryContract
{
    public function all(): StateCollection;
    public function getDictionary(StatePluckFieldVo $field, StatePluckKeyVo $key): array;
    public function getDictionaryByType(EnumDynamicVo $type, StatePluckFieldVo $field, StatePluckKeyVo $key): array;
    public function getByType(EnumDynamicVo $type): StateCollection;
    public function findByCode(EnumDynamicVo $code): StateEntity;
}
