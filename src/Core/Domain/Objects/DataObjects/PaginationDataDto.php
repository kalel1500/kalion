<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects;

class PaginationDataDto extends AbstractDataTransferObject
{
    public function __construct(
        public readonly ?int   $total,
        public readonly ?int   $last_page,
        public readonly int    $per_page,
        public readonly int    $current_page,
        public readonly string $path,
        public readonly string $page_name,
        public readonly string $html_links,
    )
    {
    }
}
