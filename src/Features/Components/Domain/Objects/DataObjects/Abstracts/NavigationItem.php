<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Abstracts;

use Illuminate\Support\Str;
use Thehouseofel\Kalion\Core\Domain\Objects\DataObjects\AbstractDataTransferObject;
use Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Navbar\Items\NavbarDropdownDto;
use Thehouseofel\Kalion\Features\Components\Domain\Objects\DataObjects\Sidebar\Items\Collections\SidebarItemCollection;

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
        $this->hasDropdown = ! is_null($dropdown);
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
