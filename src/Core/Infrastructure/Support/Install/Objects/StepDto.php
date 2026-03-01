<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Objects;

use Illuminate\Console\Command;

class StepDto
{
    public function __construct(
        public readonly ?Command     $command,
        public readonly bool         $withExamples,
        public readonly bool         $developMode,
        public readonly bool         $keepMigrationsDate,
        public readonly string       $pathGenBase,
        public readonly string       $pathGenExamples,
        public readonly string|array $stepPaths,
        public string|array          $from,
        public readonly string|array $to,
        public readonly string|array $up_from,
        public readonly string|array $down_from,
        public readonly string|array $examples_from,
    )
    {
    }
}
