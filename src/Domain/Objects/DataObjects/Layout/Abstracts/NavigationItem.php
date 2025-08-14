<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Abstracts;

use Illuminate\Support\Str;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\AbstractDataTransferObject;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Collections\SidebarItemCollection;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\NavbarDropdownDto;

abstract class NavigationItem extends AbstractDataTransferObject
{
    protected bool $hasDropdown;

    public function __construct(
        public ?string                                      $code,
        public ?string                                      $icon,
        public ?string                                      $text,
        public ?string                                      $tooltip,
        public ?string                                      $route_name,
        public ?bool                                        $is_post,
        public NavbarDropdownDto|SidebarItemCollection|null $dropdown,
    )
    {
        $this->hasDropdown = !is_null($dropdown);
    }

    public function getCode(): string
    {
        return $this->code ?? Str::slug($this->text);
    }

    public function getHref(): string
    {
        return safe_route($this->route_name, '#');
    }

    public function hasDropdown(): bool
    {
        return $this->hasDropdown;
    }
}
