<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Services\RepositoryServices;

use Thehouseofel\Kalion\Domain\Contracts\Repositories\StateRepositoryContract;
use Thehouseofel\Kalion\Domain\Objects\Entities\StateEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters\StatePluckFieldVo;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Parameters\StatePluckKeyVo;
use Thehouseofel\Kalion\Domain\Providers\DynamicEnumProviderContract;

final readonly class StateDataService
{
    public function __construct(
        private StateRepositoryContract     $repository,
        private DynamicEnumProviderContract $dynamicEnumProvider
    )
    {
    }

    public function findByCode(string $code): StateEntity
    {
        return $this->repository->findByCode($this->dynamicEnumProvider::newEnum($code));
    }

    public function getDictionaryByType(string $type, string $field = 'id', string $key = 'code'): array
    {
        $type   = $this->dynamicEnumProvider::newEnum($type);
        $field  = StatePluckFieldVo::new($field);
        $key    = StatePluckKeyVo::new($key);
        return $this->repository->getDictionaryByType($type, $field, $key);
    }
}
