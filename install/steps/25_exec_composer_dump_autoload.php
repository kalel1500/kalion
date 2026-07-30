<?php

declare(strict_types=1);

use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Install\StepBase;

#[Step(
    paths      : '',
    title      : 'Running "composer dump-autoload" command',
    skip       : false,
    getPathFrom: null
)]
class ExecComposerDumpAutoload extends StepBase
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
            ['composer', 'dump-autoload'],
            '"composer dump-autoload" command successfully.',
            '"composer dump-autoload" command has failed.',
        );
    }
}
