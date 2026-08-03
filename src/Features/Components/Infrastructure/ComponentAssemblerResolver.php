<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Features\Components\Infrastructure;

use Thehouseofel\Kalion\Features\Components\Infrastructure\Assemblers\LayoutAppAssembler;
use Thehouseofel\Kalion\Features\Components\Infrastructure\Assemblers\NavbarFullAssembler;
use Thehouseofel\Kalion\Features\Components\Infrastructure\Assemblers\SidebarFullAssembler;

class ComponentAssemblerResolver
{
    private LayoutAppAssembler $layoutApp;
    private NavbarFullAssembler $navbarFull;
    private SidebarFullAssembler $sidebarFull;

    public function layoutApp(): LayoutAppAssembler
    {
        return $this->layoutApp ??= new LayoutAppAssembler();
    }

    public function navbarFull(): NavbarFullAssembler
    {
        return $this->navbarFull ??= new NavbarFullAssembler();
    }

    public function sidebarFull(): SidebarFullAssembler
    {
        return $this->sidebarFull ??= new SidebarFullAssembler();
    }
}

