<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Title;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;

#[Step(
    paths      : 'resources',
    title      : '%s carpeta "resources"',
    skip       : false,
    getPathFrom: Step::BASE
)]
class CopyResources extends StepBase
{
    #[Title('Copiando')]
    public function up(): void
    {
        File::deleteDirectory($this->data->to);
        File::ensureDirectoryExists($this->data->to);
        File::copyDirectory($this->data->from, $this->data->to);

        if ($this->data->withExamples) {
            File::copyDirectory($this->data->examples_from, $this->data->to);
        }
    }

    #[Title('Restaurando')]
    public function down(): void
    {
        File::deleteDirectory($this->data->to);
        File::ensureDirectoryExists($this->data->to);
        File::copyDirectory($this->data->from, $this->data->to);
    }
}
