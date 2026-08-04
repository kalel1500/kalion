<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Install\Attributes\Title;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Install\StepBase;

#[Step(
    paths      : 'app/Providers/DependencyServiceProvider.php',
    title      : '%s DependencyServiceProvider',
    skip       : false,
    getPathFrom: Step::EXAMPLES
)]
class CopyDependencyServiceProvider extends StepBase
{
    #[Title('Copiando')]
    public function up(): void
    {
        $this->down();

        $this->skipWithoutExamples();

        File::copy($this->data->from, $this->data->to);
    }

    #[Title('Eliminando')]
    public function down(): void
    {
        File::delete($this->data->to);
    }
}
