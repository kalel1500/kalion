<?php

declare(strict_types=1);

namespace Src\Posts\Domain\Objects\DataObjects;

use Src\Shared\Domain\Objects\Entities\Collections\PostCollection;
use Src\Shared\Domain\Objects\Entities\Collections\TagCollection;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\ContractDataObject;

final class ViewDataPostListDo extends ContractDataObject
{
    public function __construct(
        public readonly TagCollection  $tags,
        public readonly PostCollection $posts,
        public readonly int            $count_posts,
        public readonly ?string        $selected_tag,
    )
    {
    }
}
