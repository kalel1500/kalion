<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Shared\Domain\Contracts\Repositories;

use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\Collections\StatusCollection;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\StatusEntity;

interface StatusRepository
{
    public function all(): StatusCollection;

    public function searchByType(string $type): StatusCollection;

    public function findByCode(string $code): StatusEntity;
}
