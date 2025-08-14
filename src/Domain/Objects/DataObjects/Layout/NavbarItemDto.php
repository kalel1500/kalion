<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Abstracts\NavigationItem;

final class NavbarItemDto extends NavigationItem
{
    public function __construct(
        ?string           $code,
        ?string           $icon,
        ?string           $text,
        public ?string    $time,
        ?string           $tooltip,
        ?string           $route_name,
        ?bool             $is_post,
        public ?bool      $is_theme_toggle,
        public ?bool      $is_user,
        public ?bool      $is_separator,
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

    protected static function createFromArray(array $data): static
    {
        return new static(
            $data['code'] ?? null,
            $data['icon'] ?? null,
            $data['text'] ?? null,
            $data['time'] ?? null,
            $data['tooltip'] ?? null,
            $data['route_name'] ?? null,
            $data['is_post'] ?? null,
            $data['is_theme_toggle'] ?? null,
            $data['is_user'] ?? null,
            $data['is_separator'] ?? null,
            NavbarDropdownDto::fromArray($data['dropdown'] ?? null)
        );
    }
}
