<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Title;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;

#[Step(
    paths      : 'app/Http',
    title      : '%s carpeta "app/Http"',
    skip       : false,
    getPathFrom: null
)]
class DeleteHttp extends StepBase
{
    #[Title('Eliminando')]
    public function up(): void
    {
        if ($this->data->withExamples) {
            File::deleteDirectory($this->data->to);
        } else {
            $this->callDown();
        }
    }

    #[Title('Restaurando')]
    public function down(): void
    {
        File::ensureDirectoryExists($this->data->to);
        File::copyDirectory($this->data->from, $this->data->to);
    }
}
