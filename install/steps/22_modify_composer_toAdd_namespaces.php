<?php

declare(strict_types=1);

use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Title;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;

#[Step(
    paths      : 'composer.json',
    title      : '%s namespaces al "composer.json"',
    skip       : false,
    getPathFrom: null
)]
class ModifyComposerToAddNamespaces extends StepBase
{
    protected array $namespaces = ['Src\\' => 'src/'];

    #[Title('Añadiendo')]
    public function up(): void
    {
        if ($this->data->withExamples) {
            $this->modifyComposerJson(
                function (array $composer) {
                    $psr4       = $composer['autoload']['psr-4'] ?? [];

                    $composer['autoload']['psr-4'] = $this->namespaces + $psr4;
                    ksort($composer['autoload']['psr-4']);

                    return $composer;
                }
            );
        } else {
            $this->callDown();
        }
    }

    #[Title('Eliminando')]
    public function down(): void
    {
        $this->modifyComposerJson(
            function (array $composer) {
                foreach ($this->namespaces as $ns => $_) {
                    unset($composer['autoload']['psr-4'][$ns]);
                }

                return $composer;
            }
        );
    }
}
