<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services\Commands;

use Composer\InstalledVersions;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\ServiceProvider;
use RuntimeException;
use Thehouseofel\Kalion\Domain\Traits\CountMethods;
use Thehouseofel\Kalion\Infrastructure\Console\Commands\KalionStart;
use Thehouseofel\Kalion\Infrastructure\KalionServiceProvider;
use Thehouseofel\Kalion\Infrastructure\Services\Version;
use Throwable;
use function Illuminate\Filesystem\join_paths;

/**
 * @internal This class is not meant to be used or overwritten outside the package.
 */
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
            $allFiles = File::allFiles($path, true);
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
        $exists = File::exists($this->lockFilePath);
        if ($exists && $this->reset) {
            File::delete($this->lockFilePath);
            return;
        }

        $timestamp = now()->toDateTimeString();
        if ($this->developMode && $exists) {
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
     * Reads, transforms, and writes composer.json
     * @param \Closure(array): array $callback receives current json array and returns modified
     * @param string $message message to output after writing
     */
    private function transformComposerJson(\Closure $callback, string $message): static
    {
        $this->number++;
        $file = base_path('composer.json');
        if (! file_exists($file)) {
            return $this;
        }

        $composer = json_decode(file_get_contents($file), true); // , 512, JSON_THROW_ON_ERROR
        $composer = $callback($composer);

        $json = json_encode($composer, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL; //  | JSON_THROW_ON_ERROR
        $json = preg_replace_callback(
            '/"keywords": \[\s+([^]]+?)\s+]/s',
            function (array $matches) {
                $keywords = preg_replace('/\s+/', '', $matches[1]);  // Elimina espacios y saltos de línea
                $keywords = str_replace('","', '", "', $keywords);   // Añade un espacio después de cada coma
                return '"keywords": [' . $keywords . ']';
            },
            $json
        );

        file_put_contents($file, $json);

        $this->line($message);

        return $this;
    }

    /**
     * Execute a process.
     *
     * @throws \RuntimeException
     */
    private function execute_Process(array|string $command, ?string $startMessage, string $successMessage, string $failureMessage, bool $show_number = true): void
    {
        try {
            // Imprimir mensaje de inicio del proceso
            if (! is_null($startMessage)) {
                $this->line($startMessage, false);
            }

            // Ejecutamos el proceso
            $run = Process::run($command);

            // Verificamos si el proceso falló
            if ($run->failed()) {
                throw new RuntimeException();
            }

            // Imprimimos el mensaje de éxito
            $this->line($successMessage, $show_number);
        } catch (Throwable $exception) {
            $failureMessageEnd = ' Please run the following command manually: "' . implode(' ', $command) . '"';
            $this->command->warn($failureMessage . $failureMessageEnd);
            $errorMessage = isset($run) ? $run->errorOutput() : $exception->getMessage();
            $this->command->error($errorMessage);
        }
    }

    /**
     * @throws \RuntimeException
     * @throws \LogicException
     */
    private function installDependenciesWithComposer(): static
    {
        // Install "tightenco/ziggy" -> composer require tightenco/ziggy (execute_Process)

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

    private function addDependenciesManuallyInComposerJson(): static
    {
        return $this->transformComposerJson(
            function (array $composer) {
                $packages = ['tightenco/ziggy' => '^2.5'];
                $require  = $composer['require'] ?? [];

                if ($this->reset) {
                    foreach (array_keys($packages) as $pkg) {
                        unset($require[$pkg]);
                    }
                } else {
                    foreach ($packages as $pkg => $ver) {
                        $require[$pkg] = $ver;
                    }
                }

                $composer['require'] = $require;

                return $composer;
            },
            'Dependencias actualizadas en "composer.json"'
        );
    }


    public static function configure(KalionStart $command, bool $reset, bool $simple): static
    {
        return new static($command, $reset, $simple);
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

    public function stubsCopyFiles_Js(): static
    {
        $this->number++;

        if ($this->reset || $this->simple) {
            // Delete ".prettierrc"
            File::delete(base_path('.prettierrc'));

            // Delete "tsconfig.json"
            File::delete(base_path('tsconfig.json'));

            // Delete "vite.config.ts"
            File::delete(base_path('vite.config.ts'));
            File::copy($this->originalStubsPath('vite.config.js'), base_path('vite.config.js'));
        } else {
            // Copy ".prettierrc"
            File::copy($this->stubsPath('.prettierrc', true), base_path('.prettierrc'));

            // Copy "tsconfig.json"
            File::copy($this->stubsPath('tsconfig.json', true), base_path('tsconfig.json'));

            // Copy "vite.config.ts"
            File::delete(base_path('vite.config.js'));
            File::copy($this->stubsPath('vite.config.ts', true), base_path('vite.config.ts'));
        }

        $action = $this->reset ? 'eliminados' : 'copiados';
        $this->line('Archivos Js ' . $action);

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
        $dest = base_path($folder);



        if ($this->reset) {
            $dir = $this->originalStubsPath($folder);
            File::deleteDirectory($dest);
            File::ensureDirectoryExists($dest);
            File::copyDirectory($dir, $dest);
        } else {
            $dir = $this->stubsPath($folder);
            File::ensureDirectoryExists($dest);
            File::copyDirectory($dir, $dest);

            if (! $this->simple) {
                $dirFront = $this->stubsPath($folder, true);
                File::copyDirectory($dirFront, $dest);
                File::delete(resource_path('js/app.js'));
                File::delete(resource_path('js/bootstrap.js'));
            }
        }

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

        $message = 'Archivo "resources/js/bootstrap.js" modificado';

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
                $this->line($message);
                return $this;
            }
            // Add the import line to the beginning of the file
            $fileContents = $importLine . PHP_EOL . $fileContents;
        }

        file_put_contents($filePath, $fileContents);

        $this->line($message);

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

    public function modifyFile_PackageJson_toAddNpmDependencies(): static
    {
        $this->number++;

        $packages = [
            'flowbite'                    => '3.1.2',
            '@types/node'                 => '22.15.18',
            'prettier'                    => '3.5.3',
            'prettier-plugin-blade'       => '2.1.21',
            'prettier-plugin-tailwindcss' => '0.6.11',
            'typescript'                  => '5.8.3',
            '@kalel1500/kalion-js'        => '0.9.1-beta.1',
        ];

        $versions = [];
        foreach ($packages as $package => $defaultVersion) {
            try {
                $this->line('=> Consultando version ' . $package, false);
                $result = Http::get('https://registry.npmjs.org/'.$package.'/latest');
                if ($result->failed()) {
                    throw new ConnectionException();
                }
                $versions[$package] = $result->json()['version'];
            } catch (Throwable $e) {
                $versions[$package] = $defaultVersion;
            }
        }

        // Install NPM packages...
        $this->modifyPackageJsonSection('devDependencies', [
            'flowbite' => '^' . $versions['flowbite'],
        ], $this->reset);

        // Install NPM packages...
        $this->modifyPackageJsonSection('devDependencies', [
            '@types/node'                 => '^' . $versions['@types/node'],
            'prettier'                    => '^' . $versions['prettier'],
            'prettier-plugin-blade'       => '^' . $versions['prettier-plugin-blade'],
            'prettier-plugin-tailwindcss' => '^' . $versions['prettier-plugin-tailwindcss'],
            'typescript'                  => '^' . $versions['typescript'],
        ], ($this->reset || $this->simple));

        $this->modifyPackageJsonSection('dependencies', [
            '@kalel1500/kalion-js' => $versions['@kalel1500/kalion-js'],
        ], ($this->reset || $this->simple));

        $this->line('Archivo package.json actualizado (dependencies)');

        return $this;
    }

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
            // 'npm'  => config('kalion.version_npm'),
        ], ($this->reset || $this->simple));

        $this->line('Archivo package.json actualizado (engines)');

        return $this;
    }

    public function modifyFile_ComposerJson_toAddSrcNamespace(): static
    {
        return $this->transformComposerJson(
            function (array $composer) {
                $namespaces = ['Src\\' => 'src/'];
                $psr4       = $composer['autoload']['psr-4'] ?? [];

                if ($this->reset) {
                    foreach ($namespaces as $ns => $_) {
                        unset($composer['autoload']['psr-4'][$ns]);
                    }
                } else {
                    $composer['autoload']['psr-4'] = $namespaces + $psr4;
                    ksort($composer['autoload']['psr-4']);
                }

                return $composer;
            },
            'Namespace "Src" actualizado en "composer.json"'
        );
    }

    public function modifyFile_ComposerJson_toAddHelperFilePath(): static
    {
        return $this->transformComposerJson(
            function (array $composer) {
                $files   = $composer['autoload']['files'] ?? [];
                $helpers = [
                    'src/Shared/Domain/Helpers/helpers_domain.php',
                    'src/Shared/Infrastructure/Helpers/helpers_infrastructure.php',
                ];

                if ($this->reset) {
                    $composer['autoload']['files'] = array_filter(
                        $files,
                        fn($file) => ! in_array($file, $helpers, true)
                    );
                    if (empty($composer['autoload']['files'])) {
                        unset($composer['autoload']['files']);
                    }
                } else {
                    foreach ($helpers as $file) {
                        if (! in_array($file, $files, true)) {
                            $composer['autoload']['files'][] = $file;
                        }
                    }
                }

                return $composer;
            },
            sprintf("Archivos helpers %s en \"composer.json\"", $this->reset ? 'eliminados' : 'añadidos')
        );
    }

    public function executeComposerRequire_or_ModifyFileComposerJson_toInstallOrAddComposerDependencies(): static
    {
        $this->number++;

        if ($this->developMode) {
            return $this->addDependenciesManuallyInComposerJson();
        } else {
            try {
                return $this->installDependenciesWithComposer();
            } catch (Throwable $_) {
                return $this->addDependenciesManuallyInComposerJson();
            }
        }
    }

    public function execute_ComposerDumpAutoload(): static
    {
        $this->number++;

        $this->execute_Process(
            ['composer', 'dump-autoload'],
            null,
            'Command "composer dump-autoload" successfully.',
            'The command "composer dump-autoload" has failed'
        );

        return $this;
    }

    /*public function execute_NpmInstallDependencies(): static
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
    }*/

    /*public function execute_NpxKalionJs(): static
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
    }*/

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
