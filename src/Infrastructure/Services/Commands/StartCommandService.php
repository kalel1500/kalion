<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services\Commands;

use Composer\InstalledVersions;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\ServiceProvider;
use Thehouseofel\Kalion\Domain\Traits\CountMethods;
use Thehouseofel\Kalion\Infrastructure\Console\Commands\KalionStart;
use Thehouseofel\Kalion\Infrastructure\KalionServiceProvider;
use Thehouseofel\Kalion\Infrastructure\Services\Version;
use function Illuminate\Filesystem\join_paths;

final class StartCommandService
{
    use CountMethods;

    private readonly string $stubsPath;
    private readonly string $stubsPathFront;
    private readonly string $originalStubsPath;

    private readonly int        $steps;
    private int                 $number                  = 0;
    private readonly bool       $developMode;
    private readonly bool       $keepMigrationsDate;
    private bool                $resourcesFolderRestored = false;
    private readonly string     $packageVersion;
    private readonly string     $lockFilePath;
    private readonly array      $stubFilesRelativePaths;

    public function __construct(
        private readonly KalionStart $command,
        private readonly bool        $reset,
        private readonly bool        $simple,
    )
    {
        if (! Version::laravelMin12()) {
            $command->error('Por ahora este comando solo esta preparado para la version de laravel 12');
            exit(1); // Terminar la ejecución con código de error
        }

        $stubsBasePath           = KALION_PATH . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR;
        $this->stubsPath         = $stubsBasePath . 'generate' . DIRECTORY_SEPARATOR . 'simple';
        $this->stubsPathFront    = $stubsBasePath . 'generate' . DIRECTORY_SEPARATOR . 'front';
        $this->originalStubsPath = $stubsBasePath . 'original';

        $this->steps                  = $this->countPublicMethods();
        $this->developMode            = config('kalion.package_in_develop');
        $this->keepMigrationsDate     = config('kalion.keep_migrations_date');
        $this->packageVersion         = InstalledVersions::getVersion('kalel1500/kalion') ?? 'dev';
        $this->lockFilePath           = base_path('kalion.lock');
        $this->stubFilesRelativePaths = $this->getStubFiles();
        $this->saveLock();
    }

    private function packagePath($path = ''): string
    {
        return join_paths(KALION_PATH, $path);
    }

    private function stubsPath($path = '', $isFront = false): string
    {
        $stubsPath = $isFront ? $this->stubsPathFront : $this->stubsPath;
        return join_paths($stubsPath, $path);
    }

    private function originalStubsPath($path = ''): string
    {
        return join_paths($this->originalStubsPath, $path);
    }

    private function getStubFiles(): array
    {
        $paths = [
            $this->stubsPath(),
            $this->stubsPath('', true)
        ];

        $relativePaths = [];
        foreach ($paths as $path) {
            $allFiles = File::allFiles($path);
            foreach ($allFiles as $file) {
                $relativePaths[] = ltrim(str_replace($path, '', $file->getRealPath()), DIRECTORY_SEPARATOR);
            }
        }
        return $relativePaths;
    }

    private function deleteLastVersionFiles(): void
    {
        if (! File::exists($this->lockFilePath) || $this->reset) {
            return;
        }

        $old         = json_decode(File::get($this->lockFilePath), true);
        $lastVersion = $old['version'];
        $oldFiles    = $old['stubs'];

        if (version_compare($lastVersion, $this->packageVersion, '=')) {
            return;
        }

        $toDelete = array_diff($oldFiles, $this->stubFilesRelativePaths);
        foreach ($toDelete as $rel) {
            $full = base_path($rel);
            if (File::exists($full)) {
                File::delete($full);
                $this->command->info("  → Eliminado obsoleto: $rel");
                // si la carpeta queda vacía, la eliminamos también
                $dir = dirname($full);
                if (File::isDirectory($dir) && count(File::files($dir)) === 0) {
                    File::deleteDirectory($dir);
                    $this->command->info("  → Carpeta vacía eliminada: " . str_replace(base_path() . '/', '', $dir));
                }
            }
        }

    }

    private function saveLock(): void
    {
        $timestamp = now()->toDateTimeString();
        if ($this->developMode && File::exists($this->lockFilePath)) {
            $old         = json_decode(File::get($this->lockFilePath), true);
            $timestamp = $old['timestamp'];
        }
        $payload = [
            'package'   => 'kalel1500/kalion',
            'version'   => $this->packageVersion,
            'timestamp' => $timestamp,
            'stubs'     => $this->stubFilesRelativePaths,
        ];

        $body = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;
        File::put($this->lockFilePath, $body);
    }

    /**
     * Write a string as indented output.
     */
    private function line(string $message, bool $show_number = true): void
    {
        $number = $show_number ? "<fg=yellow>$this->number/$this->steps</>" : '';
        $this->command->line("  - $number $message");
    }

    /**
     * Update the "package.json" file.
     */
    private function modifyPackageJsonSection(string $configurationKey, array $items, bool $remove = false): void
    {
        $filePath = base_path('package.json');

        if (! file_exists($filePath)) {
            return;
        }

        $packages = json_decode(file_get_contents($filePath), true);

        // Obtenemos la sección que se va a modificar o un array vacío si no existe
        $currentSection = $packages[$configurationKey] ?? [];

        if ($remove) {
            // Eliminamos los elementos especificados
            foreach ($items as $key => $value) {
                unset($currentSection[$key]);
            }

            // Si la sección queda vacía, la eliminamos completamente
            if (empty($currentSection)) {
                unset($packages[$configurationKey]);
            } else {
                $packages[$configurationKey] = $currentSection;
            }
        } else {
            // Añadimos los elementos a la sección
            $packages[$configurationKey] = $items + $currentSection;
            ksort($packages[$configurationKey]);
        }

        // Guardamos los cambios en package.json
        file_put_contents(
            $filePath,
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );
    }

    /**
     * Execute a process.
     */
    private function execute_Process(array|string $command, ?string $startMessage, string $successMessage, string $failureMessage, bool $show_number = true): void
    {
        // Imprimir mensaje de inicio del proceso
        if (! is_null($startMessage)) {
            $this->line($startMessage, false);
        }

        // Ejecutamos el proceso
        $run = Process::run($command);

        // Verificamos si el proceso falló
        if ($run->failed()) {
            $failureMessageEnd = ' Please run the following command manually: "' . implode(' ', $command) . '"';
            $this->command->warn($failureMessage . $failureMessageEnd);
            $this->command->error($run->errorOutput());
        } else {
            // Imprimimos el mensaje de éxito
            $this->line($successMessage, $show_number);
        }
    }

    private function restoreResources(): void
    {
        if ($this->resourcesFolderRestored) return;

        $folder = 'resources';
        $dir    = $this->originalStubsPath($folder);
        $dest   = base_path($folder);

        File::deleteDirectory($dest);
        File::ensureDirectoryExists($dest);
        File::copyDirectory($dir, $dest);
        $this->resourcesFolderRestored = true;
    }


    public static function configure(KalionStart $command, bool $reset, bool $simple): static
    {
        return new static($command, $reset, $simple);
    }

    public function restoreFilesModifiedByPackageKalionJs(): static
    {
        $this->number++;

        // Restore "resources"
        $this->restoreResources();

        // Delete ".prettierrc"
        File::delete(base_path('.prettierrc'));

        // Delete "tsconfig.json"
        File::delete(base_path('tsconfig.json'));

        // Delete "vite.config.ts"
        File::delete(base_path('vite.config.ts'));
        copy($this->originalStubsPath('vite.config.js'), base_path('vite.config.js'));

        $this->line('Restaurados todos los archivos modificados por el paquete @kalel1500/kalion-js');

        return $this;
    }

    public function publishKalionConfig(): static
    {
        $this->number++;

        // Delete "config/kalion.php"
        File::delete(config_path('kalion.php'));
        File::delete(config_path('kalion_links.php'));

        if ($this->reset || $this->developMode) return $this;

        // Publish "config/kalion_links.php"
        $this->command->call('vendor:publish', ['--tag' => 'kalion-config-links']);
        $this->line('Configuración del paquete publicada: "config/kalion_links.php"');

        return $this;
    }

    public function stubsCopyFile_DependencyServiceProvider(): static
    {
        $this->number++;

        $file = 'app/Providers/DependencyServiceProvider.php';

        $from = $this->stubsPath($file);
        $to   = base_path($file);

        if ($this->reset) {
            File::delete($to);
            $this->line('Archivo "' . $file . '" eliminado');
            return $this;
        }

        copy($from, $to);
        $this->line('Archivo "' . $file . '" creado');

        return $this;
    }

    public function stubsCopyFiles_Config(): static
    {
        $this->number++;

        $folder          = 'config';
        $sourcePath      = $this->stubsPath($folder);
        $destinationPath = base_path($folder);

        $files = File::files($sourcePath);

        foreach ($files as $file) {
            $from = $file->getPathname();
            $to   = $destinationPath . DIRECTORY_SEPARATOR . $file->getFilename();

            if ($this->reset) {
                File::delete($to);
            } else {
                File::copy($from, $to);
            }
        }

        $action = $this->reset ? 'eliminados' : 'copiados';
        $this->line('Archivos de configuración ' . $action);

        return $this;
    }

    public function stubsCopyFiles_Migrations(): static
    {
        $this->number++;

        $folder = 'database/migrations';

        // Rutas de origen:
        $stubsPath   = $this->stubsPath($folder);
        $packagePath = $this->packagePath($folder);

        // Ruta de destino en la aplicación
        $destinationPath = base_path($folder);

        // Obtenemos los archivos de ambas fuentes y los combinamos
        $stubFiles    = File::files($stubsPath);
        $packageFiles = File::files($packagePath);
        $files        = array_merge($packageFiles, $stubFiles);

        // Obtenemos los nombres "originales" de los archivos que ya existen en la carpeta destino
        $existingFiles = collect(File::files($destinationPath))
            ->map(fn($f) => preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $f->getFilename()));

        $timestamp = now();

        // Lista de migraciones que NO se deben borrar en reset
        $skipFiles = [
            'create_users_table.php',
            'create_cache_table.php',
            'create_jobs_table.php',
        ];

        foreach ($files as $file) {
            // Removemos el timestamp inicial del nombre del archivo
            $originalName = preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $file->getFilename());

            if ($this->reset) {
                // Si el archivo está en la lista de los que no se deben borrar, lo omitimos.
                if (in_array($originalName, $skipFiles)) {
                    continue;
                }
                // En modo reset, buscamos si existe el archivo en destino (comparando el nombre sin timestamp)
                $existingFile = collect(File::files($destinationPath))
                    ->first(fn($f) => preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $f->getFilename()) === $originalName);

                // Si se encontró, se elimina el archivo
                if ($existingFile) {
                    File::delete($existingFile);
                }
                continue;
            }

            // Se determina el timestamp a usar en el nuevo nombre
            if ($this->keepMigrationsDate) {
                preg_match('/^(\d{4}_\d{2}_\d{2}_\d{6})_/', $file->getFilename(), $matches);
                $fileTimestamp = $matches[1] ?? $timestamp->format('Y_m_d_His');
            } else {
                $fileTimestamp = $timestamp->format('Y_m_d_His');
                $timestamp->addSecond();
            }

            // Se arma el nuevo nombre combinando el timestamp y el nombre original
            $newFileName     = $fileTimestamp . '_' . $originalName;
            $destinationFile = $destinationPath . '/' . $newFileName;

            // Si ya existe un archivo con ese nombre base, se omite la copia para evitar duplicados
            if ($existingFiles->contains($originalName)) continue;

            // Copiamos el archivo desde su ruta de origen a la ruta de destino con el nuevo nombre
            File::copy($file->getPathname(), $destinationFile);
        }

        $action = $this->reset ? 'eliminadas' : 'copiadas';
        $this->line('Migraciones ' . $action);

        return $this;
    }

    public function stubsCopyFolder_Factories(): static
    {
        $this->number++;

        // Factories
        $folder = 'database/factories';

        $dir  = ($this->reset) ? $this->originalStubsPath($folder) : $this->stubsPath($folder);
        $dest = base_path($folder);

        // Borrar para que se eliminen los archivos existentes
        File::deleteDirectory($dest);

        File::ensureDirectoryExists($dest);
        File::copyDirectory($dir, $dest);

        $this->line('Carpeta "' . $folder . '" copiada');

        return $this;
    }

    public function stubsCopyFolder_Seeders(): static
    {
        $this->number++;

        // Factories
        $folder = 'database/seeders';

        $dir  = ($this->reset) ? $this->originalStubsPath($folder) : $this->stubsPath($folder);
        $dest = base_path($folder);

        // Borrar para que se eliminen los archivos existentes
        File::deleteDirectory($dest);

        File::ensureDirectoryExists($dest);
        File::copyDirectory($dir, $dest);

        $this->line('Carpeta "' . $folder . '" copiada');

        return $this;
    }

    public function stubsCopyFolder_Lang(): static
    {
        $this->number++;

        // Views
        $folder = 'lang';

        $dir  = $this->stubsPath($folder);
        $dest = base_path($folder);

        if ($this->reset) {
            File::deleteDirectory($dest);
            $this->line('Carpeta "' . $folder . '" eliminada');
            return $this;
        }

        File::ensureDirectoryExists($dest);
        File::copyDirectory($dir, $dest);
        $this->line('Carpeta "' . $folder . '" creada');

        return $this;
    }

    public function stubsCopyFolder_Resources(): static
    {
        $this->number++;

        // Views
        $folder = 'resources';

        // Restaurar la carpeta original
        $this->restoreResources();

        if ($this->reset) return $this;

        $dir  = $this->stubsPath($folder);
        $dest = base_path($folder);

        File::ensureDirectoryExists($dest);
        File::copyDirectory($dir, $dest);
        $this->line('Carpeta "' . $folder . '" creada');

        return $this;
    }

    public function stubsCopyFolder_Src(): static
    {
        $this->number++;

        // Src
        $folder = 'src';

        $dir  = $this->stubsPath($folder);
        $dest = base_path($folder);

        if ($this->reset) {
            File::deleteDirectory($dest);
            $this->line('Carpeta "' . $folder . '" eliminada');
            return $this;
        }

        File::ensureDirectoryExists($dest);
        File::copyDirectory($dir, $dest);
        $this->line('Carpeta "' . $folder . '" creada');

        return $this;
    }

    public function stubsCopyFile_RoutesWeb(): static
    {
        $this->number++;

        // routes/web.php
        $filePath = 'routes/web.php';

        $from = ($this->reset)
            ? $this->originalStubsPath($filePath)
            : $this->stubsPath($filePath);
        $to   = base_path($filePath);

        copy($from, $to);
        $this->line('Archivo "' . $filePath . '" modificado');

        return $this;
    }

    public function createEnvFiles(): static
    {
        $this->number++;

        // Crear archivos ".env" y ".env.save.local"

        $message = 'Archivos ".env" creados';

        // Definir archivo origen (al generar)
        $file        = '.env.save.local';
        $from        = $this->stubsPath($file);
        $to_envLocal = base_path($file);

        // Definir archivo destino
        $to_env = base_path('.env');

        if ($this->reset) {
            $message = 'Archivos ".env" restaurados';

            // Eliminar archivo ".env.save.local"
            File::delete($to_envLocal);

            // Definir archivo origen (reset)
            $file        = '.env.example';
            $from        = $this->originalStubsPath($file);
            $to_envLocal = base_path($file);
        }

        // Copiar origen a ".env.save.local"
        copy($from, $to_envLocal);

        if (! $this->developMode) {
            // Copiar origen a ".env" (si no es "developMode")
            copy($from, $to_env);

            // Borrar manualmente el valor de config('app.key') para que se regenere correctamente
            config(['app.key' => '']);

            // Regenerar Key
            $this->command->call('key:generate');
        }

        $this->line($message);

        return $this;
    }

    public function deleteDirectory_Http(): static
    {
        $this->number++;

        // Delete directory "app/Http"
        $folder = 'app/Http';
        $dest   = base_path($folder);

        if ($this->reset) {
            $dir = $this->originalStubsPath($folder);
            File::ensureDirectoryExists($dest);
            File::copyDirectory($dir, $dest);
            $this->line('Carpeta "' . $folder . '" creada');
            return $this;
        }

        File::deleteDirectory($dest);
        $this->line('Directorio "' . $folder . '" eliminado');

        return $this;
    }

    public function deleteDirectory_Models(): static
    {
        $this->number++;

        // Delete directory "app/Models"
        $folder = 'app/Models';
        $dest   = base_path($folder);

        if ($this->reset) {
            $dir = $this->originalStubsPath($folder);
            File::ensureDirectoryExists($dest);
            File::copyDirectory($dir, $dest);
            $this->line('Carpeta "' . $folder . '" creada');
            return $this;
        }

        File::deleteDirectory($dest);
        $this->line('Directorio "' . $folder . '" eliminado');

        return $this;
    }

    public function deleteFile_Changelog(): static
    {
        $this->number++;

        // Delete file "CHANGELOG.md"
        File::delete(base_path('CHANGELOG.md'));
        $this->line('Archivo "CHANGELOG.md" eliminado');

        return $this;
    }

    public function modifyFile_BootstrapProviders_toAddDependencyServiceProvider(): static
    {
        $this->number++;

        // bootstrap/providers.php

        if (! Version::laravelMin11()) {
            return $this;
        }

        if ($this->reset) {
            KalionServiceProvider::removeProviderFromBootstrapFile('App\Providers\DependencyServiceProvider');
        } else {
            ServiceProvider::addProviderToBootstrapFile('App\Providers\DependencyServiceProvider');
        }

        $this->line('Archivo "bootstrap/providers.php" modificado');

        return $this;
    }

    public function modifyFile_BootstrapApp_toAddMiddlewareRedirect(): static
    {
        $this->number++;

        if (! Version::laravelMin11()) {
            return $this;
        }

        // Ruta del archivo a modificar
        $filePath = base_path('bootstrap/app.php');

        // Leer el contenido del archivo
        $content = File::get($filePath);

        // Usar una expresión regular para encontrar y modificar el bloque `withMiddleware`
        $pattern = '/->withMiddleware\(function \(Middleware \$middleware\) \{(.*?)}\)/s';

        // Reemplazar el contenido del bloque con la nueva línea
        $replacement = ($this->reset)
            ? <<<'EOD'
->withMiddleware(function (Middleware $middleware) {
        //
    })
EOD
            : <<<'EOD'
->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectUsersTo('home'); // Ruta a la que redirigir si entran en rutas con el middleware "guest" (RedirectIfAuthenticated)
    })
EOD;

        $newContent = preg_replace($pattern, $replacement, $content);

        // Guardar el archivo con el contenido actualizado
        File::put($filePath, $newContent);

        $this->line('Archivo "bootstrap/app.php" modificado para agregar redirectUsersTo en withMiddleware');

        return $this;
    }

    public function modifyFile_BootstrapApp_toAddExceptionHandler(): static
    {
        $this->number++;

        if (! Version::laravelMin11()) {
            return $this;
        }

        // Ruta del archivo a modificar
        $filePath = base_path('bootstrap/app.php');

        // Leer el contenido del archivo
        $content = File::get($filePath);

        // Usar una expresión regular para encontrar y reemplazar el bloque `withExceptions`
        $pattern = '/->withExceptions\(function \(Exceptions \$exceptions\) \{(.*?)}\)/s';

        // Reemplazar el contenido del bloque con las nuevas líneas
        $replacement = ($this->reset)
            ? <<<'EOD'
->withExceptions(function (Exceptions $exceptions) {
        //
    })
EOD
            : <<<'EOD'
->withExceptions(function (Exceptions $exceptions) {
        $callback = \Thehouseofel\Kalion\Infrastructure\Exceptions\ExceptionHandler::getUsingCallback();
        $callback($exceptions);
    })
EOD;

        $newContent = preg_replace($pattern, $replacement, $content);

        // Guardar el archivo con el contenido actualizado
        File::put($filePath, $newContent);

        $this->line('Archivo "bootstrap/app.php" modificado');

        return $this;
    }

    public function modifyFile_ConfigApp_toUpdateTimezone(): static
    {
        $this->number++;

        // Ruta del archivo a modificar
        $filePath = base_path('config/app.php');

        // Leer el contenido del archivo
        $content = File::get($filePath);

        // Reemplazar la línea específica
        if ($this->reset) {
            $updatedContent = preg_replace(
                '/\'timezone\'\s*=>\s*\'Europe\/Madrid\'/',
                "'timezone' => 'UTC'",
                $content
            );
        } else {
            $updatedContent = preg_replace(
                '/\'timezone\'\s*=>\s*\'UTC\'/',
                "'timezone' => 'Europe/Madrid'",
                $content
            );
        }

        // Guardar el archivo con el contenido actualizado
        File::put($filePath, $updatedContent);

        $this->line('Archivo "config/app.php" modificado para actualizar el timezone');

        return $this;
    }

    public function modifyFile_JsBootstrap_toAddImportFlowbite(): static
    {
        $this->number++;

        // Import "flowbite" in resources/js/bootstrap.js
        $filePath = base_path('resources/js/bootstrap.js');

        if (! file_exists($filePath)) {
            return $this;
        }

        $fileContents = file_get_contents($filePath);

        $importLine = "import 'flowbite';";

        if ($this->reset) {
            // Remove the import line from the file
            $fileContents = str_replace($importLine . PHP_EOL, '', $fileContents);
        } else {
            if (str_contains($fileContents, $importLine)) {
                return $this;
            }
            // Add the import line to the beginning of the file
            $fileContents = $importLine . PHP_EOL . $fileContents;
        }

        file_put_contents($filePath, $fileContents);

        $this->line('Archivo "resources/js/bootstrap.js" modificado');

        return $this;
    }

    public function modifyFile_Gitignore_toDeleteLockFileLines(): static
    {
        $this->number++;

        // Borrar los ".lock" del ".gitignore"

        if ($this->developMode) return $this;

        // Ruta del archivo .gitignore
        $gitignorePath    = base_path('.gitignore');
        $gitignoreContent = file($gitignorePath, FILE_IGNORE_NEW_LINES); // Leer el archivo como un array de líneas

        // Definir las líneas que queremos eliminar
        $linesToRemove = ['composer.lock', 'package-lock.json'];

        // Filtrar el contenido para eliminar solo las líneas especificadas
        $gitignoreContent = array_filter($gitignoreContent, function ($line) use ($linesToRemove) {
            return ! in_array($line, $linesToRemove, true); // Mantener líneas que no están en $linesToRemove
        });

        // Eliminar cualquier línea vacía adicional al final del contenido
        while (end($gitignoreContent) === '') {
            array_pop($gitignoreContent);
        }

        // Escribir el contenido actualizado en el archivo con una sola línea vacía al final
        file_put_contents($gitignorePath, implode(PHP_EOL, $gitignoreContent) . PHP_EOL);

        $this->line('Archivos ".lock" eliminados del ".gitignore"');

        return $this;
    }

    /*public function modifyFile_PackageJson_toAddNpmDevDependencies(): static
    {
        $this->number++;

        // Install NPM packages...
        $this->modifyPackageJsonSection('devDependencies', [
            'flowbite'                      => config('kalion.version_flowbite'),
        ], $this->reset);

        // Install NPM packages...
        $this->modifyPackageJsonSection('devDependencies', [
            '@types/node'                   => config('kalion.version_types_node'),
            'prettier'                      => config('kalion.version_prettier'),
            'prettier-plugin-blade'         => config('kalion.version_prettier_plugin_blade'),
            'prettier-plugin-tailwindcss'   => config('kalion.version_prettier_plugin_tailwindcss'),
            'typescript'                    => config('kalion.version_typescript'),
        ], ($this->reset || $this->simple));

        $this->line('Archivo package.json actualizado (devDependencies)');

        return $this;
    }*/

    /*public function modifyFile_PackageJson_toAddNpmDependencies(): static
    {
        $this->number++;

        $this->modifyPackageJsonSection('dependencies', [
            '@kalel1500/kalion-js'   => config('kalion.version_kalel1500_laravel_ts_utils'),
//            'tabulator-tables'              => config('kalion.version_tabulator_tables'),
        ], ($this->reset || $this->simple));

        $this->line('Archivo package.json actualizado (dependencies)');

        return $this;
    }*/

    public function modifyFile_PackageJson_toAddScriptTsBuild(): static
    {
        $this->number++;

        // Add script "ts-build" in "package.json"
        $this->modifyPackageJsonSection('scripts', [
            'ts-build' => 'tsc && vite build',
        ], ($this->reset || $this->simple));

        $this->line('Archivo package.json actualizado (script "ts-build")');

        return $this;
    }

    public function modifyFile_PackageJson_toAddEngines(): static
    {
        $this->number++;

        // Add script "ts-build" in "package.json"
        $this->modifyPackageJsonSection('engines', [
            'node' => config('kalion.version_node'),
            'npm'  => config('kalion.version_npm'),
        ], ($this->reset || $this->simple));

        $this->line('Archivo package.json actualizado (engines)');

        return $this;
    }

    public function modifyFile_ComposerJson_toAddSrcNamespace(): static
    {
        $this->number++;

        // Update the "autoload.psr-4" section in "composer.json" file with additional namespaces.
        // Add the "Src" namespace into "composer.json"

        $namespaces = ['Src\\' => 'src/'];

        $filePath = base_path('composer.json');

        if (! file_exists($filePath)) {
            return $this;
        }

        $composer = json_decode(file_get_contents($filePath), true);

        if (! isset($composer['autoload']['psr-4'])) {
            $composer['autoload']['psr-4'] = [];
        }

        $psr4 = $composer['autoload']['psr-4'];

        if ($this->reset) {
            // Eliminamos los namespaces especificados
            foreach ($namespaces as $namespace => $path) {
                unset($composer['autoload']['psr-4'][$namespace]);
            }
        } else {
            // Añadimos los nuevos namespaces
            $composer['autoload']['psr-4'] = $namespaces + $psr4;
            ksort($composer['autoload']['psr-4']);
        }

        // Convertir el arreglo a JSON y formatear con JSON_PRETTY_PRINT
        $jsonContent = json_encode($composer, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        // Usa una expresión regular para encontrar la key "keywords" y ponerla en una línea
        $jsonContent = preg_replace_callback(
            '/"keywords": \[\s+([^]]+?)\s+]/s',
            function ($matches) {
                // Limpia el contenido de "keywords" y colócalo en una línea
                $keywords = preg_replace('/\s+/', '', $matches[1]);  // Elimina espacios y saltos de línea
                $keywords = str_replace('","', '", "', $keywords);   // Añade un espacio después de cada coma
                return '"keywords": [' . $keywords . ']';
            },
            $jsonContent
        );

        // Guardamos los cambios en composer.json
        file_put_contents($filePath, $jsonContent . PHP_EOL);


        $this->line('Namespace "Src" añadido al "composer.json"');

        return $this;
    }

    public function modifyFile_ComposerJson_toAddHelperFilePath(): static
    {
        $this->number++;

        // Ruta del archivo composer.json
        $filePath = base_path('composer.json');

        if (! file_exists($filePath)) {
            return $this;
        }

        // Cargar el contenido actual de composer.json
        $composer = json_decode(file_get_contents($filePath), true);

        if (! isset($composer['autoload'])) {
            $composer['autoload'] = [];
        }

        if (! isset($composer['autoload']['files'])) {
            $composer['autoload']['files'] = [];
        }

        // Archivos de helpers a añadir o eliminar
        $filesToAdd = [
            "src/Shared/Domain/Helpers/helpers_domain.php",
            "src/Shared/Infrastructure/Helpers/helpers_infrastructure.php",
        ];

        if ($this->reset) {
            // Si estamos en modo reset, eliminamos los archivos de la lista
            $composer['autoload']['files'] = array_filter(
                $composer['autoload']['files'],
                fn($file) => ! in_array($file, $filesToAdd, true)
            );

            // Si la lista queda vacía, eliminamos completamente la clave "files"
            if (empty($composer['autoload']['files'])) {
                unset($composer['autoload']['files']);
            }
        } else {
            // Por defecto, agregamos los archivos si no están presentes
            foreach ($filesToAdd as $file) {
                if (! in_array($file, $composer['autoload']['files'], true)) {
                    $composer['autoload']['files'][] = $file;
                }
            }
        }

        // Convertir el arreglo a JSON y formatear con JSON_PRETTY_PRINT
        $jsonContent = json_encode($composer, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        // Usa una expresión regular para formatear la propiedad "keywords" correctamente en una línea
        $jsonContent = preg_replace_callback(
            '/"keywords": \[\s+([^]]+?)\s+]/s',
            function ($matches) {
                $keywords = preg_replace('/\s+/', '', $matches[1]);  // Elimina espacios y saltos de línea
                $keywords = str_replace('","', '", "', $keywords);   // Añade un espacio después de cada coma
                return '"keywords": [' . $keywords . ']';
            },
            $jsonContent
        );

        // Guardamos los cambios en composer.json
        file_put_contents($filePath, $jsonContent . PHP_EOL);

        $action = $this->reset ? 'eliminados de' : 'añadidos a';
        $this->line("Archivos helpers {$action} \"autoload.files\" en \"composer.json\"");

        return $this;
    }

    public function execute_ComposerRequire_toInstallComposerDependencies(): static
    {
        $this->number++;

        if ($this->developMode) return $this;

        // Install "tightenco/ziggy"

        $content = file_get_contents(base_path('composer.json'));

        $packages = ['tightenco/ziggy'];
        $package1 = $packages[0];

        if ($this->reset) {
            if (! str_contains($content, $package1)) {
                return $this;
            }

            $this->command->traitRequireComposerPackages(
                $this->command->option('composer'),
                $packages,
                true
            );

            $this->line('Dependencias de composer desinstaladas');

            return $this;
        }

        if (str_contains($content, $package1)) {
            return $this;
        }

        $this->command->traitRequireComposerPackages(
            $this->command->option('composer'),
            $packages
        );

        $this->line('Dependencias de composer instaladas');

        return $this;
    }

    /*public function execute_ComposerDumpAutoload(): static
    {
        $this->number++;

        // Execute the "composer dump-autoload" command

        if ($this->packageInDevelop) {
            return $this;
        }

        $run = Process::run('composer dump-autoload');
        if ($run->failed()) {
            $this->command->warn('The command "composer dump-autoload" has failed');
        } else {
            $this->line('Command "composer dump-autoload" successfully.');
        }

        return $this;
    }*/

    public function execute_NpmInstall(): static
    {
        $this->number++;

        if ($this->developMode) return $this;

        $this->execute_Process(
            ['npm', 'install'],
            'Installing Node dependencies.',
            'Node dependencies installed successfully.',
            'Node dependency installation failed.'
        );

        return $this;
    }

    public function execute_NpmInstallDependencies(): static
    {
        $this->number++;

        if ($this->developMode) return $this;

        $isReset         = $this->reset;
        $isResetFront    = $this->reset || $this->simple;
        $packageJsonPath = base_path('package.json');
        $packages        = [
            'devDependencies' => [
                'flowbite'                    => $isReset,
                '@types/node'                 => $isResetFront,
                'prettier'                    => $isResetFront,
                'prettier-plugin-blade'       => $isResetFront,
                'prettier-plugin-tailwindcss' => $isResetFront,
                'typescript'                  => $isResetFront,
            ],
            'dependencies'    => [
                '@kalel1500/kalion-js' => $isResetFront,
            ]
        ];

        foreach ($packages as $type => $dependencies) {
            $extra = $type === 'devDependencies' ? '--save-dev' : '';
            foreach ($dependencies as $package => $remove) {

                $exsistFolder  = File::exists(base_path("node_modules/$package"));
                $inPackageJson = false;
                if (File::exists($packageJsonPath)) {
                    $packageJson = json_decode(File::get($packageJsonPath), true);
                    if (isset($packageJson['dependencies'][$package]) || isset($packageJson['devDependencies'][$package])) {
                        $inPackageJson = true;
                    }
                }

                $is_installed = $exsistFolder && $inPackageJson;
                $skip_process = ($remove && ! $is_installed) || (! $remove && $is_installed);
                if ($skip_process) continue;

                $action = $remove ? 'uninstall' : 'install';
                $this->execute_Process(
                    ['npm', $action, $package, $extra],
                    null,
                    "=> Successfully $action $package",
                    "=> Sailed $action $package",
                    false
                );
            }
        }

        $this->line('Dependencias de NPM actualizadas');

        return $this;
    }

    public function execute_NpxKalionJs(): static
    {
        $this->number++;

        if ($this->reset || $this->simple) return $this;

        $this->execute_Process(
            ['npx', 'kalion-js'],
            'Running the "kalion-js" package start command.',
            'kalion-js package files generated successfully.',
            'Error while generating files for the "kalion-js" package.'
        );

        return $this;
    }

    public function stubsCopyFolder_ResourcesFront(): static
    {
        $this->number++;

        // Views
        $folder = 'resources';

        if ($this->reset || $this->simple) return $this;

        $dir  = $this->stubsPath($folder, true);
        $dest = base_path($folder);

        File::ensureDirectoryExists($dest);
        File::copyDirectory($dir, $dest);
        $this->line('Carpeta "' . $folder . '" creada');

        return $this;
    }

    public function execute_gitAdd(): static
    {
        $this->number++;

        $this->execute_Process(
            ['git', 'add', '.'],
            null,
            'New files added to the Git Staged Area.',
            'Error adding new files to the Git Staged Area.'
        );

        return $this;
    }

    public function execute_NpmRunBuild(): static
    {
        $this->number++;

        if ($this->developMode) return $this;

        $this->execute_Process(
            ['npm', 'run', 'build'],
            'Building app.',
            'App built successfully.',
            'Build failed.'
        );

        return $this;
    }

}
