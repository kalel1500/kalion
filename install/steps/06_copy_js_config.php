<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Title;
use function Illuminate\Filesystem\join_paths;

#[Step(
    paths        : ['.prettierrc', 'tsconfig.json', 'vite.config.ts', 'vite.config.js'],
    title        : '%s archivos de configuración del Front',
    skip         : false,
    isExamplePath: false
)]
class CopyJsConfig extends StepBase
{
    #[Title('Copiando')]
    public function up(): void
    {
//        File::copy(join_paths($this->data->from, '.prettierrc'), join_paths($this->data->to, '.prettierrc'));
//        File::copy(join_paths($this->data->from, 'tsconfig.json'), join_paths($this->data->to, 'tsconfig.json'));
//        File::copy(join_paths($this->data->from, 'vite.config.ts'), join_paths($this->data->to, 'vite.config.ts'));
//        File::delete(join_paths($this->data->to, 'vite.config.js'));

        foreach ($this->data->to as $key => $to) {
            if (str_contains($to, 'vite.config.js')) {
                File::delete($to);
            } else {
                File::copy($this->data->from[$key], $to);
            }
        }
    }

    #[Title('Eliminando')]
    public function down(): void
    {
//        File::delete(join_paths($this->data->to, '.prettierrc'));
//        File::delete(join_paths($this->data->to, 'tsconfig.json'));
//        File::delete(join_paths($this->data->to, 'vite.config.ts'));
//        File::copy(join_paths($this->data->from, 'vite.config.js'), join_paths($this->data->to, 'vite.config.js'));

        foreach ($this->data->to as $key => $to) {
            if (str_contains($to, 'vite.config.js')) {
                File::copy($this->data->from[$key], $to);
            } else {
                File::delete($to);
            }
        }
    }
}
