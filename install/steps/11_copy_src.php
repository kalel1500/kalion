<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Title;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;

#[Step(
    paths      : 'src',
    title      : '%s carpeta "src"',
    skip       : false,
    getPathFrom: Step::EXAMPLES
)]
class CopySrc extends StepBase
{
    #[Title('Copiando')]
    public function up(): void
    {
        $this->callDown();

        $this->skipWithoutExamples();

        File::ensureDirectoryExists($this->data->to);
        File::copyDirectory($this->data->from, $this->data->to);
    }

    #[Title('Eliminando')]
    public function down(): void
    {
        File::deleteDirectory($this->data->to);
    }
}
