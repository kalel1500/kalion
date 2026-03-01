<?php

declare(strict_types=1);

use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;

#[Step(
    paths      : 'package.json',
    title      : 'Actualizando archivo "package.json" (engines)',
    skip       : false,
    getPathFrom: null
)]
class ModifyPackageToAddEngines extends StepBase
{
    protected array $engines;

    public function prepare(): void
    {
        $this->engines = [
            'node' => config('kalion.command.start.version_node'),
            // 'npm'  => config('kalion.version_npm'),
        ];
    }

    public function up(): void
    {
        $this->modifyPackageJsonSection('engines', $this->engines, false);
    }

    public function down(): void
    {
        $this->modifyPackageJsonSection('engines', $this->engines, true);
    }
}
