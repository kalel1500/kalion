<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Shared\Domain\Contracts\Repositories;

use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Primitives\StringVo;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\Collections\RoleCollection;
use Thehouseofel\Kalion\Features\Shared\Domain\Objects\Entities\RoleEntity;

interface RoleRepository
{
    public function all(): RoleCollection;

    public function findByName(StringVo $name): RoleEntity;
}
