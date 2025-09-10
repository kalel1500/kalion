<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Repositories;

use Thehouseofel\Kalion\Domain\Objects\Entities\RoleEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;

interface RoleRepository
{
    public function findByName(ModelString $name): RoleEntity;
}
