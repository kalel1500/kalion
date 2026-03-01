<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Title;

#[Step(
    paths        : 'config/kalion_links.php',
    title        : '%s configuración del paquete: "config/kalion_links.php"',
    skip         : false,
    isExamplePath: true
)]
class PublishConfig extends StepBase
{
    #[Title('Publicando')]
    public function up(): void
    {
        $this->down();

        if ($this->data->developMode) {
            return;
        }

        $this->data->command->callSilent('vendor:publish', ['--tag' => 'kalion-config-links']);
    }

    #[Title('Despublicando')]
    public function down(): void
    {
        File::delete($this->data->to);
    }
}
