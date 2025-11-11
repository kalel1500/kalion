<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Shared\Domain\Contracts\Repositories;

use Thehouseofel\Kalion\Core\Domain\Objects\Entities\RoleEntity;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;

interface RoleRepository
{
    public function findByName(StringVo $name): RoleEntity;
}
