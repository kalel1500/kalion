<?php

declare(strict_types=1);

use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;

#[Step(
    paths      : 'package.json',
    title      : 'Actualizando archivo "package.json" (script "ts-build")',
    skip       : false,
    getPathFrom: null
)]
class ModifyPackageToAddScriptTsBuild extends StepBase
{
    protected array $scripts = [
        'ts-build' => 'tsc && vite build',
    ];

    public function up(): void
    {
        $this->modifyPackageJsonSection('scripts', $this->scripts, false);
    }

    public function down(): void
    {
        $this->modifyPackageJsonSection('scripts', $this->scripts, true);
    }
}
