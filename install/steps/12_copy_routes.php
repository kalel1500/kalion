<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Title;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;

#[Step(
    paths      : 'routes/web.php',
    title      : '%s archivo "routes/web.php"',
    skip       : false,
    getPathFrom: Step::BOTH
)]
class CopyRoutes extends StepBase
{
    #[Title('Copiando')]
    public function up(): void
    {
        File::copy($this->data->from, $this->data->to);
    }

    #[Title('Restaurando')]
    public function down(): void
    {
        File::copy($this->data->from, $this->data->to);
    }
}
