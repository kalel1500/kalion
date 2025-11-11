<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Layout;

use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\Layout\Abstracts\NavigationItem;

final class NavbarItemDto extends NavigationItem
{
    public function __construct(
        ?string            $code,
        ?string            $icon,
        ?string            $text,
        public ?string     $time,
        ?string            $tooltip,
        ?string            $route_name,
        ?bool              $is_post,
        public ?bool       $is_theme_toggle,
        public ?bool       $is_user,
        public ?bool       $is_separator,
        ?NavbarDropdownDto $dropdown
    )
    {
        parent::__construct(
            $code,
            $icon,
            $text,
            $tooltip,
            $route_name,
            $is_post,
            $dropdown,
        );
    }
}
