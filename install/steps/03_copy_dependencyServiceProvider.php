<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Title;

#[Step(
    paths        : 'app/Providers/DependencyServiceProvider.php',
    title        : '%s DependencyServiceProvider',
    skip         : false,
    isExamplePath: true
)]
class CopyDependencyServiceProvider extends StepBase
{
    #[Title('Copiando')]
    public function up(): void
    {
        $this->down();

        if ($this->data->withExamples) {
            File::copy($this->data->from, $this->data->to);
        }
    }

    #[Title('Eliminando')]
    public function down(): void
    {
        File::delete($this->data->to);
    }
}
