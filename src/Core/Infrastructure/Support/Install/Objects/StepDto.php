<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Objects;

use Illuminate\Console\Command;

readonly class StepDto
{
    public function __construct(
        public ?Command     $command,
        public bool         $withExamples,
        public bool         $developMode,
        public bool         $keepMigrationsDate,
        public string       $pathGenBase,
        public string       $pathGenExamples,
        public string|array $stepPaths,
        public string|array $fromUp,
        public string|array $from,
        public string|array $to,
    )
    {
    }
}
