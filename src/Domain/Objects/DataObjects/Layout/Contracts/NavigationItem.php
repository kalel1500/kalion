<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Contracts;

use Thehouseofel\Kalion\Domain\Objects\DataObjects\ContractDataObject;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\Collections\SidebarItemCollection;
use Thehouseofel\Kalion\Domain\Objects\DataObjects\Layout\NavbarDropdownDo;

abstract class NavigationItem extends ContractDataObject
{
    protected bool $hasDropdown;

    public function __construct(
        public ?string                                     $code,
        public ?string                                     $icon,
        public ?string                                     $text,
        public ?string                                     $tooltip,
        public ?string                                     $route_name,
        public ?bool                                       $is_post,
        public NavbarDropdownDo|SidebarItemCollection|null $dropdown,
    )
    {
        $this->hasDropdown = !is_null($dropdown);
    }

    public function getCode(): string
    {
        return $this->code ?? strToSlug($this->text);
    }

    public function getHref(): string
    {
        return getUrlFromRoute($this->route_name);
    }

    public function hasDropdown(): bool
    {
        return $this->hasDropdown;
    }
}
