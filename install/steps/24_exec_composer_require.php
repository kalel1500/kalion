<?php

declare(strict_types=1);

use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Title;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;

#[Step(
    paths      : 'composer.json',
    title      : '%s dependencias de composer',
    skip       : false,
    getPathFrom: null
)]
class ExecComposerRequire extends StepBase
{
    protected array $dependencies = [
        'tightenco/ziggy' => '^2.6'
    ];

    #[Title('Instalando')]
    public function up(): void
    {
        $this->common(false);
    }

    #[Title('Desinstalando')]
    public function down(): void
    {
        $this->common(true);
    }

    protected function common(bool $rollback): void
    {
        if ($this->data->developMode) {
            $this->modifyComposerToAddDependencies($rollback);
        } else {
            try {
                $this->data->command->callRequireComposerPackages(
                    composer: $this->data->command->option('composer'),
                    packages: array_keys($this->dependencies),
                    isRemove: $rollback
                );
            } catch (Throwable $_) {
                $this->modifyComposerToAddDependencies($rollback);
            }
        }
    }

    protected function modifyComposerToAddDependencies($rollback): void
    {
        $this->modifyComposerJson(
            function (array $composer) use ($rollback) {
                $require  = $composer['require'] ?? [];

                if ($rollback) {
                    foreach (array_keys($this->dependencies) as $pkg) {
                        unset($require[$pkg]);
                    }
                } else {
                    foreach ($this->dependencies as $pkg => $ver) {
                        $require[$pkg] = $ver;
                    }
                }

                $composer['require'] = $require;

                return $composer;
            }
        );
    }
}
