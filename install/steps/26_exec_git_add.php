<?php

declare(strict_types=1);

use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Title;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;

#[Step(
    paths      : '',
    title      : 'Running "git .add" command',
    skip       : false,
    getPathFrom: null
)]
class ExecGitAdd extends StepBase
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
        $this->execute_Process(
            ['git', 'add', '.'],
            'New files added to the Git Staged Area',
            'Error adding new files to the Git Staged Area',
        );
    }
}
