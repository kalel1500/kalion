<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;

#[Step(
    paths      : 'package.json',
    title      : 'Actualizando archivo "package.json" (dependencies)',
    skip       : false,
    getPathFrom: null
)]
class ModifyPackageToAddDependencies extends StepBase
{
    protected array $dependencies = [
        '@kalel1500/kalion-js'        => '^0.10.0-beta.0',
        '@types/node'                 => '^22.15.24',
        'flowbite'                    => '^3.1.2',
        'prettier'                    => '^3.6.2',
        'prettier-plugin-blade'       => '^2.1.21',
        'prettier-plugin-tailwindcss' => '^0.7.1',
        'typescript'                  => '^5.9.3',
    ];

    public function up(): void
    {
        if (! $this->data->developMode) {
            foreach ($this->dependencies as $package => $defaultVersion) {
                try {
                    $this->print('=> Consultando version ' . $package);
                    $result = Http::throw()->get('https://registry.npmjs.org/' . $package . '/latest');
                    $this->dependencies[$package] = '^' . $result->json()['version'];
                } catch (Throwable $e) {
                }
            }
        }

        $this->modifyPackageJsonSection('devDependencies', $this->dependencies, false);
    }

    public function down(): void
    {
        $this->modifyPackageJsonSection('devDependencies', $this->dependencies, true);
    }
}
