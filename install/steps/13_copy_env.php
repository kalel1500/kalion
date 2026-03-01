<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Title;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;

#[Step(
    paths      : '.env.save.local',
    title      : '%s archivo ".env.save.local"',
    skip       : false,
    getPathFrom: Step::BASE
)]
class CopyEnv extends StepBase
{
    #[Title('Copiando')]
    public function up(): void
    {
        File::copy($this->data->from, $this->data->to);
    }

    #[Title('Restaurando')]
    public function down(): void
    {
        File::delete($this->data->to);
    }
}
