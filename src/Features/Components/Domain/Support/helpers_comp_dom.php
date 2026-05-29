<?php

use Thehouseofel\Kalion\Features\Components\Domain\Support\LayoutMetrics;

if (! function_exists('get_rounded_class')) {
    function get_rounded_class($variant): string
    {
        return LayoutMetrics::ROUNDED_VARIANTS[$variant];
    }
}
