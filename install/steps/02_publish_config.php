<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Install\Attributes\Title;
use Thehouseofel\Kalion\Core\Infrastructure\Utilities\Install\StepBase;

#[Step(
    paths      : 'config/kalion_links.php',
    title      : '%s configuración del paquete: "config/kalion_links.php"',
    skip       : false,
    getPathFrom: Step::EXAMPLES
)]
class PublishConfig extends StepBase
{
    #[Title('Publicando')]
    public function up(): void
    {
        $this->down();

        $this->skipOnDevelop('Skipped "vendor:publish" in develop mode');

        $this->data->command->callSilent('vendor:publish', ['--tag' => 'kalion-config-links']);
    }

    #[Title('Despublicando')]
    public function down(): void
    {
        File::delete($this->data->to);
    }
}
