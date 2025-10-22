<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Contracts\Repositories;

use Src\Shared\Domain\Objects\Entities\Collections\TagTypeCollection;
use Src\Shared\Domain\Objects\Entities\TagTypeEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\Primitives\StringVo;

interface TagTypeRepository
{
    public function all(): TagTypeCollection;
    public function findByCode(StringVo $code): TagTypeEntity;
}
