<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Install\Attributes\Title;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Install\StepBase;

#[Step(
    paths      : 'database/factories',
    title      : '%s carpeta "database/factories"',
    skip       : false,
    getPathFrom: Step::EXAMPLES
)]
class CopyFactories extends StepBase
{
    #[Title('Copiando')]
    public function up(): void
    {
        if ($this->data->withExamples) {
            File::deleteDirectory($this->data->to);
            File::ensureDirectoryExists($this->data->to);
            File::copyDirectory($this->data->from, $this->data->to);
        } else {
            $this->callDown();
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
