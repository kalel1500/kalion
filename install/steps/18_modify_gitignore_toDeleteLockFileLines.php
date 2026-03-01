<?php

declare(strict_types=1);

use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;

#[Step(
    paths      : '.gitignore',
    title      : 'Modify .gitignore to delete lock file lines',
    skip       : false,
    getPathFrom: null
)]
class ModifyGitignoreToDeleteLockFileLines extends StepBase
{
    public function up(): void
    {
        $this->skipOnDevelop('Skipped ".gitignore" deletions in developMode');

        $gitignoreContent = file($this->data->to, FILE_IGNORE_NEW_LINES);
        $linesToRemove    = ['composer.lock', 'package-lock.json'];

        $kippedContent = array_filter($gitignoreContent, function ($line) use ($linesToRemove) {
            return ! in_array($line, $linesToRemove, true); // Mantener líneas que no están en $linesToRemove
        });

        // Eliminar cualquier línea vacía adicional al final del contenido
        while (end($kippedContent) === '') {
            array_pop($kippedContent);
        }

        // Escribir el contenido actualizado en el archivo con una sola línea vacía al final
        file_put_contents($this->data->to, implode(PHP_EOL, $kippedContent) . PHP_EOL);
    }

    public function down(): void
    {
    }
}
