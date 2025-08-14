<?php

declare(strict_types=1);

namespace Src\Posts\Domain\Objects\DataObjects;

use Src\Shared\Domain\Objects\Entities\Collections\PostCollection;
use Src\Shared\Domain\Objects\Entities\Collections\TagCollection;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\AbstractDataObject;

final class ViewDataPostListDo extends AbstractDataObject
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
