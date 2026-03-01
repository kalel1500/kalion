<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Objects;

readonly class InstallDto
{
    public function __construct(
        public bool    $reset,
        public bool    $withExamples,
        public bool    $developMode,
        public bool    $keepMigrationsDate,
        public string  $pattern,
        public string  $pathGenBase,
        public string  $pathGenExamples,
        public string  $pathOriginal,
        public ?string $selectedStep,
    )
    {
    }
}
