<?php

declare(strict_types=1);

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Step;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\StepBase;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Attributes\Title;

#[Step(
    paths        : 'database/migrations',
    title        : '%s migraciones',
    skip         : false,
    isExamplePath: true
)]
class CopyMigrations extends StepBase
{
    private const MIGRATION_PREFIX_PATTERN = '/^(\d{4}_\d{2}_\d{2}_\d{6})_/';

    protected readonly string     $pathKalionMigrations;
    protected readonly Carbon     $now;
    protected readonly Collection $currentAppMigrations;
    protected readonly array      $migrationsToKeepInRollback;

    public function prepare(): void
    {
        $this->pathKalionMigrations       = normalize_path(KALION_PATH . '/' . $this->data->stepPaths);
        $this->now                        = now();
        $this->currentAppMigrations       = collect(File::files($this->data->to))->map(fn($f) => $this->getMigrationBaseName($f));
        $this->migrationsToKeepInRollback = ['create_users_table.php', 'create_cache_table.php', 'create_jobs_table.php',];
    }

    #[Title('Copiando')]
    public function up(): void
    {
        /**
         * @param \Symfony\Component\Finder\SplFileInfo[] $files
         * @return void
         */
        $copyMigrations = function (array $files) {
            foreach ($files as $file) {
                $baseName = $this->getMigrationBaseName($file);

                if ($this->currentAppMigrations->contains($baseName)) {
                    continue;
                }

                // Se determina el timestamp a usar en el nuevo nombre
                if ($this->data->keepMigrationsDate) {
                    preg_match(self::MIGRATION_PREFIX_PATTERN, $file->getFilename(), $matches);
                    $fileTimestamp = $matches[1] ?? $this->now->format('Y_m_d_His');
                } else {
                    $fileTimestamp = $this->now->format('Y_m_d_His');
                    $this->now->addSecond();
                }

                File::copy($file->getPathname(), $this->data->to . '/' . $fileTimestamp . '_' . $baseName);
            }
        };

        $copyMigrations(File::files($this->pathKalionMigrations));
        if ($this->data->withExamples) {
            $copyMigrations(File::files($this->data->from));
        }
    }

    #[Title('Eliminando')]
    public function down(): void
    {
        /**
         * @param \Symfony\Component\Finder\SplFileInfo[] $files
         * @return void
         */
        $copyMigrations = function (array $files) {
            foreach ($files as $file) {
                $baseName = $this->getMigrationBaseName($file);

                // Si el archivo está en la lista de los que no se deben borrar, lo omitimos.
                if (in_array($baseName, $this->migrationsToKeepInRollback)) {
                    continue;
                }

                // En modo reset, buscamos si existe el archivo en destino (comparando el nombre sin timestamp)
                /** @var \Symfony\Component\Finder\SplFileInfo|null $existingFile */
                $existingFile = collect(File::files($this->data->to))->first(fn($f) => $this->getMigrationBaseName($f) === $baseName);

                // Si se encontró, se elimina el archivo
                if ($existingFile) {
                    File::delete($existingFile);
                }
            }
        };

        $copyMigrations(File::files($this->pathKalionMigrations));
        $copyMigrations(File::files($this->data->fromUp));
    }

    protected function getMigrationBaseName(\Symfony\Component\Finder\SplFileInfo $file): string
    {
        return preg_replace(self::MIGRATION_PREFIX_PATTERN, '', $file->getFilename());
    }
}
