<?php

declare(strict_types=1);

use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Title;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;

#[Step(
    paths      : '',
    title      : 'Building app',
    skip       : false,
    getPathFrom: null
)]
class ExecNpmRunBuild extends StepBase
{
    public function up(): void
    {
        $this->common();
    }

    public function down(): void
    {
        $this->common();
    }

    protected function common(): void
    {
        $this->skipOnDevelop('Skipped "npm run build" in develop mode');

        $this->execute_Process(
            ['npm', 'run', 'build'],
            'App built successfully',
            'Build failed',
        );
    }
}
