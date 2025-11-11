<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Contracts\Repositories;

use Thehouseofel\Kalion\Domain\Objects\Entities\RoleEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\StringVo;

interface RoleRepository
{
    public function findByName(StringVo $name): RoleEntity;
}
