<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Install\Attributes\Title;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Install\StepBase;

#[Step(
    paths      : 'app/Models',
    title      : '%s carpeta "app/Models"',
    skip       : false,
    getPathFrom: null
)]
class DeleteModels extends StepBase
{
    #[Title('Eliminando')]
    public function up(): void
    {
        if ($this->data->withExamples) {
            File::deleteDirectory($this->data->to);
        } else {
            $this->down($this->data->from);
        }
    }

    #[Title('Restaurando')]
    public function down($from = null): void
    {
        File::ensureDirectoryExists($this->data->to);
        File::copyDirectory($from ?? $this->data->from, $this->data->to);
    }
}
