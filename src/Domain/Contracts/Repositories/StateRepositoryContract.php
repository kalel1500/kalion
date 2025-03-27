<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Repositories;

use Thehouseofel\Kalion\Domain\Objects\Entities\Collections\StateCollection;
use Thehouseofel\Kalion\Domain\Objects\Entities\StateEntity;

interface StateRepositoryContract
{
    public function all(): StateCollection;
    public function searchByType(string $type): StateCollection;
    public function findByCode(string $code): StateEntity;
}
