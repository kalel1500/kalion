<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Contracts\Repositories;

use Src\Shared\Domain\Objects\Entities\Collections\PostCollection;
use Src\Shared\Domain\Objects\Entities\PostEntity;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelId;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelString;
use Thehouseofel\Kalion\Domain\Objects\ValueObjects\EntityFields\ModelStringNull;

interface PostRepository
{
    public function all(): PostCollection;
    public function searchByTag(ModelStringNull $tag_code): PostCollection;
    public function find(ModelId $id): PostEntity;
    public function findBySlug(ModelString $slug): PostEntity;
}
