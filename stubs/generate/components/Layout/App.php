<?php

namespace Src\Shared\Infrastructure\View\Vendor\Kal\Components\Layout;

class App extends \Thehouseofel\Kalion\Core\Infrastructure\View\Components\Layout\App
{
    public function __construct(
        ?string $title = null,
        bool $package = false
    )
    {
        parent::__construct($title, $package);
    }

}
