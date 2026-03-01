<?php

declare(strict_types=1);

use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Title;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;

#[Step(
    paths      : 'composer.json',
    title      : '%s files al "composer.json"',
    skip       : false,
    getPathFrom: null
)]
class ModifyComposerToAddFiles extends StepBase
{
    protected array $files = [
        'src/Shared/Domain/Helpers/helpers_domain.php',
        'src/Shared/Infrastructure/Helpers/helpers_infrastructure.php',
    ];

    #[Title('Añadiendo')]
    public function up(): void
    {
        if ($this->data->withExamples) {
            $this->modifyComposerJson(
                function (array $composer) {
                    $files   = $composer['autoload']['files'] ?? [];

                    foreach ($this->files as $file) {
                        if (! in_array($file, $files, true)) {
                            $composer['autoload']['files'][] = $file;
                        }
                    }

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
                $files   = $composer['autoload']['files'] ?? [];

                $composer['autoload']['files'] = array_filter(
                    $files,
                    fn($file) => ! in_array($file, $this->files, true)
                );

                if (empty($composer['autoload']['files'])) {
                    unset($composer['autoload']['files']);
                }

                return $composer;
            }
        );
    }
}
