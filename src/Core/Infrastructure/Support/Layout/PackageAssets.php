<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Layout;

class PackageAssets
{
    protected const DIST = KALION_PATH . '/public/build/';

    public static function css(): string
    {
        return '<style>' . file_get_contents(PackageAssets::DIST . 'styles.css') . '</style>';
    }

    public static function js(): string
    {
        return '<script type="module">' . file_get_contents(PackageAssets::DIST . 'scripts.js') . '</script>';
    }
}
