<?php

declare(strict_types=1);

use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Title;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;

#[Step(
    paths      : '',
    title      : 'Installing Node dependencies',
    skip       : false,
    getPathFrom: null
)]
class ExecNpmInstall extends StepBase
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
        $this->skipOnDevelop('Skipped "npm install" in develop mode');

        $this->execute_Process(
            ['npm', 'install'],
            'Node dependencies installed successfully',
            'Node dependency installation failed',
        );
    }
}
