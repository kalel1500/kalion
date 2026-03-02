<?php

declare(strict_types=1);

use Illuminate\Support\Facades\File;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Title;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;

#[Step(
    paths      : 'bootstrap/app.php',
    title      : 'Modificando archivo "bootstrap/app.php" para %s el "ExceptionHandler" en el "withExceptions()"',
    skip       : false,
    getPathFrom: null
)]
class ModifyBAppToAddExceptionHandler extends StepBase
{
    #[Title('añadir')]
    public function up(): void
    {
        $this->modify(<<<'EOD'
->withExceptions(function (Exceptions $exceptions)$1 {
        \Thehouseofel\Kalion\Core\Infrastructure\Laravel\Exceptions\ExceptionHandler::handle($exceptions);
    })
EOD);
    }

    #[Title('quitar')]
    public function down(): void
    {
        $this->modify(<<<'EOD'
->withExceptions(function (Exceptions $exceptions)$1 {
        //
    })
EOD);
    }

    protected function modify(string $replacement): void
    {
        $content = File::get($this->data->to);

        // Usar una expresión regular para encontrar y reemplazar el bloque `withExceptions`
        $pattern = '/->withExceptions\(function\s*\(Exceptions\s+\$exceptions\)(:\s*void)?\s*\{(.*?)\}\)/s';

        $newContent = preg_replace($pattern, $replacement, $content);

        File::put($this->data->to, $newContent);
    }
}
