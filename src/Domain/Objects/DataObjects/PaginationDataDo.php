<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects;

final class PaginationDataDo extends AbstractDataObject
{
    public function __construct(
        public readonly int $total,
        public readonly int $lastPage,
        public readonly int $perPage,
        public readonly int $currentPage,
        public readonly ?string $path,
        public readonly string $pageName,
        public readonly string $htmlLinks
    )
    {
    }
}
