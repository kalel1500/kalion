<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Title;

#[Step(
    paths      : 'config',
    title      : '%s archivos de configuración',
    skip       : false,
    getPathFrom: Step::EXAMPLES
)]
class CopyConfig extends StepBase
{
    #[Title('Copiando')]
    public function up(): void
    {
        $this->skipWithoutExamples();

        $this->common(function (string $from, string $to) {
            File::copy($from, $to);
        });
    }

    #[Title('Eliminando')]
    public function down(): void
    {
        $this->common(function (string $from, string $to) {
            File::delete($to);
        });
    }

    protected function common(callable $callback): void
    {
        $files = File::files($this->data->up_from);

        foreach ($files as $file) {
            $from = $file->getPathname();
            $to   = normalize_path("{$this->data->to}/{$file->getFilename()}");
            $callback($from, $to);
        }
    }
}
