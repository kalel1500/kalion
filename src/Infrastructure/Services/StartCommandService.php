<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\ServiceProvider;
use Thehouseofel\Kalion\Domain\Traits\CountMethods;
use Thehouseofel\Kalion\Infrastructure\Console\Commands\KalionStart;
use Thehouseofel\Kalion\Infrastructure\KalionServiceProvider;

final class StartCommandService
{
    use CountMethods;

    private $command;
    private $reset;
    private $simple;
    private $steps;
    private $number = 0;
    private $filesystem;
    private $developMode;
    private $keepMigrationsDate;
    private $resourcesFolderRestored = false;

    public function __construct(KalionStart $command, bool $reset, bool $simple)
    {
        if (!Version::laravelMin12()) {
            $command->error('Por ahora este comando solo esta preparado para la version de laravel 12');
            exit(1); // Terminar la ejecución con código de error
        }
        $this->command          = $command;
        $this->reset            = $reset;
        $this->simple           = $simple;
        $this->steps            = $this->countPublicMethods();
        $this->filesystem       = $command->filesystem();
        $this->developMode      = config('kalion.package_in_develop');
        $this->keepMigrationsDate = config('kalion.keep_migrations_date');
    }

    private function isReset(bool $isFront = false): bool
    {
        return $isFront ? ($this->reset || $this->simple) : $this->reset;
    }

    /**
     * Write a string as indented output.
     *
     * @param string $message
     * @param bool $show_number
     * @return void
     */
    private function line(string $message, bool $show_number = true): void
    {
        $number = $show_number ? "<fg=yellow>$this->number/$this->steps</>" : '';
        $this->command->line("  - $number $message");
    }

    /**
     * Update the "package.json" file.
     *
     * @param string $configurationKey
     * @param array $items
     * @param bool $remove
     * @return void
     */
    private function modifyPackageJsonSection(string $configurationKey, array $items, bool $remove = false)
    {
        $filePath = base_path('package.json');

        if (!file_exists($filePath)) {
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
     *
     * @param array|string $command
     * @param string|null $startMessage
     * @param string $successMessage
     * @param string $failureMessage
     * @return void
     */
    private function execute_Process($command, ?string $startMessage, string $successMessage, string $failureMessage): void
    {
        // Imprimir mensaje de inicio del proceso
        if (!is_null($startMessage)) {
            $this->line($startMessage, false);
        }

        // Ejecutamos el proceso
        $run = Process::run($command);

        // Verificamos si el proceso falló
        if ($run->failed()) {
            $failureMessageEnd = ' Please run the following command manually: "' . implode(' ', $command) . '"';
            $this->command->warn($failureMessage.$failureMessageEnd);
            $this->command->error($run->errorOutput());
        } else {
            // Imprimimos el mensaje de éxito
            $this->line($successMessage);
        }
    }

    private function restoreResources(): void
    {
        if ($this->resourcesFolderRestored) return;

        $folder = 'resources';
        $dir = $this->command->originalStubsPath($folder);
        $dest = base_path($folder);

        $this->filesystem->deleteDirectory($dest);
        $this->filesystem->ensureDirectoryExists($dest);
        $this->filesystem->copyDirectory($dir, $dest);
        $this->resourcesFolderRestored = true;
    }


    public static function configure(KalionStart $command, bool $reset, bool $simple): self
    {
        return new self($command, $reset, $simple);
    }

    public function restoreFilesModifiedByPackageLaravelTsUtils(): self
    {
        $this->number++;

        // Restore "resources"
        $this->restoreResources();

        // Delete ".prettierrc"
        $this->filesystem->delete(base_path('.prettierrc'));

        // Delete "tsconfig.json"
        $this->filesystem->delete(base_path('tsconfig.json'));

        // Delete "vite.config.ts"
        $this->filesystem->delete(base_path('vite.config.ts'));
        copy($this->command->originalStubsPath('vite.config.js'), base_path('vite.config.js'));

        $this->line('Restaurados todos los archivos modificados por el paquete @kalel1500/laravel-ts-utils');

        return $this;
    }

    public function publishKalionConfig(): self
    {
        $this->number++;

        // Delete "config/kalion.php"
        $this->filesystem->delete(config_path('kalion.php'));
        $this->filesystem->delete(config_path('kalion_auth.php'));
        $this->filesystem->delete(config_path('kalion_layout.php'));
        $this->filesystem->delete(config_path('kalion_links.php'));

        if ($this->isReset() || $this->developMode) return $this;

        // Publish "config/kalion.php"
        $this->command->call('vendor:publish', ['--tag' => 'kalion-config-auth']);
        $this->command->call('vendor:publish', ['--tag' => 'kalion-config-links']);
        $this->line('Configuración del paquete publicada: "config/kalion_auth.php" y "config/kalion_links.php"');

        return $this;
    }

    public function stubsCopyFile_AppServiceProvider(): self
    {
        $this->number++;

        if ($this->developMode) return $this;

        $file = 'app/Providers/AppServiceProvider.php';

        $from = ($this->isReset()) ? $this->command->originalStubsPath($file) : $this->command->stubsPath($file);
        $to = base_path($file);

        copy($from, $to);
        $this->line('Archivo "'.$file.'" creado');

        return $this;
    }

    public function stubsCopyFile_DependencyServiceProvider(): self
    {
        $this->number++;

        $file = 'app/Providers/DependencyServiceProvider.php';

        $from = $this->command->stubsPath($file);
        $to = base_path($file);

        if ($this->isReset()) {
            $this->filesystem->delete($to);
            $this->line('Archivo "'.$file.'" eliminado');
            return $this;
        }

        copy($from, $to);
        $this->line('Archivo "'.$file.'" creado');

        return $this;
    }

    public function stubsCopyFiles_Config(): self
    {
        $this->number++;

        $folder = 'config';
        $sourcePath = $this->command->stubsPath($folder);
        $destinationPath = base_path($folder);

        $files = $this->filesystem->files($sourcePath);

        foreach ($files as $file) {
            $from = $file->getPathname();
            $to = $destinationPath . DIRECTORY_SEPARATOR . $file->getFilename();

            if ($this->isReset()) {
                $this->filesystem->delete($to);
            } else {
                $this->filesystem->copy($from, $to);
            }
        }

        $action = $this->reset ? 'eliminados' : 'copiados';
        $this->line('Archivos de configuración '.$action);

        return $this;
    }

    public function stubsCopyFiles_Migrations(): self
    {
        $this->number++;

        $folder = 'database/migrations';
        $sourcePath = $this->command->stubsPath($folder);
        $destinationPath = base_path($folder);

        $files = $this->filesystem->files($sourcePath);
        $existingFiles = collect($this->filesystem->files($destinationPath))->map(fn($f) => preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $f->getFilename()));
        $timestamp = now();

        foreach ($files as $file) {
            $originalName = preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $file->getFilename());

            if ($this->reset) {
                $existingFile = collect($this->filesystem->files($destinationPath))->first(fn($f) => preg_replace('/^\d{4}_\d{2}_\d{2}_\d{6}_/', '', $f->getFilename()) === $originalName);

                // Comprobar que SI exista el archivo
                if ($existingFile) {
                    $this->filesystem->delete($existingFile);
                }

                continue;
            }

            if ($this->keepMigrationsDate) {
                preg_match('/^(\d{4}_\d{2}_\d{2}_\d{6})_/', $file->getFilename(), $matches);
                $fileTimestamp = $matches[1] ?? $timestamp->format('Y_m_d_His');
            } else {
                $fileTimestamp = $timestamp->format('Y_m_d_His');
                $timestamp->addSecond();
            }

            $newFileName = $fileTimestamp . '_' . $originalName;
            $destinationFile = $destinationPath . '/' . $newFileName;

            // Comprobar que no exista el archivo
            if ($existingFiles->contains($originalName)) continue;

            $this->filesystem->copy($file->getPathname(), $destinationFile);
        }

        $action = $this->reset ? 'eliminadas' : 'copiadas';
        $this->line('Migraciones '.$action);

        return $this;
    }

    public function stubsCopyFolder_Factories(): self
    {
        $this->number++;

        // Factories
        $folder = 'database/factories';

        $dir = ($this->isReset()) ? $this->command->originalStubsPath($folder) : $this->command->stubsPath($folder);
        $dest = base_path($folder);

        // Borrar para que se eliminen los archivos existentes
        $this->filesystem->deleteDirectory($dest);

        $this->filesystem->ensureDirectoryExists($dest);
        $this->filesystem->copyDirectory($dir, $dest);

        $this->line('Carpeta "'.$folder.'" copiada');

        return $this;
    }

    public function stubsCopyFolder_Seeders(): self
    {
        $this->number++;

        // Factories
        $folder = 'database/seeders';

        $dir = ($this->isReset()) ? $this->command->originalStubsPath($folder) : $this->command->stubsPath($folder);
        $dest = base_path($folder);

        // Borrar para que se eliminen los archivos existentes
        $this->filesystem->deleteDirectory($dest);

        $this->filesystem->ensureDirectoryExists($dest);
        $this->filesystem->copyDirectory($dir, $dest);

        $this->line('Carpeta "'.$folder.'" copiada');

        return $this;
    }

    public function stubsCopyFolder_Lang(): self
    {
        $this->number++;

        // Views
        $folder = 'lang';

        $dir = $this->command->stubsPath($folder);
        $dest = base_path($folder);

        if ($this->isReset()) {
            $this->filesystem->deleteDirectory($dest);
            $this->line('Carpeta "'.$folder.'" eliminada');
            return $this;
        }

        $this->filesystem->ensureDirectoryExists($dest);
        $this->filesystem->copyDirectory($dir, $dest);
        $this->line('Carpeta "'.$folder.'" creada');

        return $this;
    }

    public function stubsCopyFolder_Resources(): self
    {
        $this->number++;

        // Views
        $folder = 'resources';

        // Restaurar la carpeta original
        $this->restoreResources();

        if ($this->isReset()) return $this;

        $dir = $this->command->stubsPath($folder);
        $dest = base_path($folder);

        $this->filesystem->ensureDirectoryExists($dest);
        $this->filesystem->copyDirectory($dir, $dest);
        $this->line('Carpeta "'.$folder.'" creada');

        return $this;
    }

    public function stubsCopyFolder_Src(): self
    {
        $this->number++;

        // Src
        $folder = 'src';

        $dir = $this->command->stubsPath($folder);
        $dest = base_path($folder);

        if ($this->isReset()) {
            $this->filesystem->deleteDirectory($dest);
            $this->line('Carpeta "'.$folder.'" eliminada');
            return $this;
        }

        $this->filesystem->ensureDirectoryExists($dest);
        $this->filesystem->copyDirectory($dir, $dest);
        $this->line('Carpeta "'.$folder.'" creada');

        return $this;
    }

    public function stubsCopyFile_RoutesWeb(): self
    {
        $this->number++;

        // routes/web.php
        $originalFile = 'routes/web.php';
        $generatedFile = 'routes/'.(Version::phpMin74() ? 'web.php' : 'web_php_old.php');

        $from = ($this->isReset()) ? $this->command->originalStubsPath($originalFile) : $this->command->stubsPath($generatedFile);
        $to = base_path($originalFile);

        copy($from, $to);
        $this->line('Archivo "'.$originalFile.'" modificado');

        return $this;
    }

    public function createEnvFiles(): self
    {
        $this->number++;

        // Crear archivos ".env" y ".env.save.local"

        $message = 'Archivos ".env" creados';

        // Definir archivo origen (al generar)
        $file = '.env.save.local';
        $from = $this->command->stubsPath($file);
        $to_envLocal = base_path($file);

        // Definir archivo destino
        $to_env = base_path('.env');

        if ($this->isReset()) {
            $message = 'Archivos ".env" restaurados';

            // Eliminar archivo ".env.save.local"
            $this->filesystem->delete($to_envLocal);

            // Definir archivo origen (reset)
            $file = '.env.example';
            $from = $this->command->originalStubsPath($file);
            $to_envLocal = base_path($file);
        }

        // Copiar origen a ".env.save.local"
        copy($from, $to_envLocal);

        if (!$this->developMode) {
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

    public function deleteDirectory_Http(): self
    {
        $this->number++;

        // Delete directory "app/Http"
        $folder = 'app/Http';
        $dest = base_path($folder);

        if ($this->isReset()) {
            $dir = $this->command->originalStubsPath($folder);
            $this->filesystem->ensureDirectoryExists($dest);
            $this->filesystem->copyDirectory($dir, $dest);
            $this->line('Carpeta "'.$folder.'" creada');
            return $this;
        }

        $this->filesystem->deleteDirectory($dest);
        $this->line('Directorio "'.$folder.'" eliminado');

        return $this;
    }

    public function deleteDirectory_Models(): self
    {
        $this->number++;

        // Delete directory "app/Models"
        $folder = 'app/Models';
        $dest = base_path($folder);

        if ($this->isReset()) {
            $dir = $this->command->originalStubsPath($folder);
            $this->filesystem->ensureDirectoryExists($dest);
            $this->filesystem->copyDirectory($dir, $dest);
            $this->line('Carpeta "'.$folder.'" creada');
            return $this;
        }

        $this->filesystem->deleteDirectory($dest);
        $this->line('Directorio "'.$folder.'" eliminado');

        return $this;
    }

    public function deleteFile_Changelog(): self
    {
        $this->number++;

        // Delete file "CHANGELOG.md"
        $this->filesystem->delete(base_path('CHANGELOG.md'));
        $this->line('Archivo "CHANGELOG.md" eliminado');

        return $this;
    }

    public function modifyFile_BootstrapProviders_toAddDependencyServiceProvider(): self
    {
        $this->number++;

        // bootstrap/providers.php

        if (!Version::laravelMin11()) {
            return $this;
        }

        if ($this->isReset()) {
            KalionServiceProvider::removeProviderFromBootstrapFile('App\Providers\DependencyServiceProvider');
        } else {
            ServiceProvider::addProviderToBootstrapFile('App\Providers\DependencyServiceProvider');
        }

        $this->line('Archivo "bootstrap/providers.php" modificado');

        return $this;
    }

    public function modifyFile_BootstrapApp_toAddMiddlewareRedirect(): self
    {
        $this->number++;

        if (!Version::laravelMin11()) {
            return $this;
        }

        // Ruta del archivo a modificar
        $filePath = base_path('bootstrap/app.php');

        // Leer el contenido del archivo
        $content = File::get($filePath);

        // Usar una expresión regular para encontrar y modificar el bloque `withMiddleware`
        $pattern = '/->withMiddleware\(function \(Middleware \$middleware\) \{(.*?)}\)/s';

        // Reemplazar el contenido del bloque con la nueva línea
        $replacement = ($this->isReset())
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

    public function modifyFile_BootstrapApp_toAddExceptionHandler(): self
    {
        $this->number++;

        if (!Version::laravelMin11()) {
            return $this;
        }

        // Ruta del archivo a modificar
        $filePath = base_path('bootstrap/app.php');

        // Leer el contenido del archivo
        $content = File::get($filePath);

        // Usar una expresión regular para encontrar y reemplazar el bloque `withExceptions`
        $pattern = '/->withExceptions\(function \(Exceptions \$exceptions\) \{(.*?)}\)/s';

        // Reemplazar el contenido del bloque con las nuevas líneas
        $replacement = ($this->isReset())
            ?  <<<'EOD'
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

    public function modifyFile_ConfigApp_toUpdateTimezone(): self
    {
        $this->number++;

        // Ruta del archivo a modificar
        $filePath = base_path('config/app.php');

        // Leer el contenido del archivo
        $content = File::get($filePath);

        // Reemplazar la línea específica
        if ($this->isReset()) {
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

    public function modifyFile_ConfigAuth_toUpdateModel(): self
    {
        $this->number++;

        // Ruta del archivo a modificar
        $filePath = base_path('config/auth.php');

        // Leer el contenido del archivo
        $content = File::get($filePath);

        // Reemplazar la línea específica
        if ($this->isReset()) {
            $updatedContent = preg_replace(
                '/\'model\'\s*=>\s*env\(\'AUTH_MODEL\',\s*Src\\\\Shared\\\\Infrastructure\\\\Models\\\\User::class\)/',
                "'model' => env('AUTH_MODEL', App\\\\Models\\\\User::class)",
                $content
            );
        } else {
            $updatedContent = preg_replace(
                '/\'model\'\s*=>\s*env\(\'AUTH_MODEL\',\s*App\\\\Models\\\\User::class\)/',
                "'model' => env('AUTH_MODEL', Src\\\\Shared\\\\Infrastructure\\\\Models\\\\User::class)",
                $content
            );
        }

        // Guardar el archivo con el contenido actualizado
        File::put($filePath, $updatedContent);

        $this->line('Archivo "config/auth.php" modificado para actualizar el modelo de usuario');

        return $this;
    }

    public function modifyFile_JsBootstrap_toAddImportFlowbite(): self
    {
        $this->number++;

        // Import "flowbite" in resources/js/bootstrap.js
        $filePath = base_path('resources/js/bootstrap.js');

        if (!file_exists($filePath)) {
            return $this;
        }

        $fileContents = file_get_contents($filePath);

        $importLine = "import 'flowbite';";

        if ($this->isReset()) {
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

    public function modifyFile_Gitignore_toDeleteLockFileLines(): self
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
            return !in_array($line, $linesToRemove, true); // Mantener líneas que no están en $linesToRemove
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

    public function modifyFile_PackageJson_toAddNpmDevDependencies(): self
    {
        $this->number++;

        // Install NPM packages...
        $this->modifyPackageJsonSection('devDependencies', [
            'flowbite'                      => config('kalion.version_flowbite'),
        ], $this->isReset());

        // Install NPM packages...
        $this->modifyPackageJsonSection('devDependencies', [
            '@types/node'                   => config('kalion.version_types_node'),
            'prettier'                      => config('kalion.version_prettier'),
            'prettier-plugin-blade'         => config('kalion.version_prettier_plugin_blade'),
            'prettier-plugin-tailwindcss'   => config('kalion.version_prettier_plugin_tailwindcss'),
            'typescript'                    => config('kalion.version_typescript'),
        ], $this->isReset(true));

        $this->line('Archivo package.json actualizado (devDependencies)');

        return $this;
    }

    public function modifyFile_PackageJson_toAddNpmDependencies(): self
    {
        $this->number++;

        $this->modifyPackageJsonSection('dependencies', [
            '@kalel1500/laravel-ts-utils'   => config('kalion.version_kalel1500_laravel_ts_utils'),
//            'tabulator-tables'              => config('kalion.version_tabulator_tables'),
        ], $this->isReset(true));

        $this->line('Archivo package.json actualizado (dependencies)');

        return $this;
    }

    public function modifyFile_PackageJson_toAddScriptTsBuild(): self
    {
        $this->number++;

        // Add script "ts-build" in "package.json"
        $this->modifyPackageJsonSection('scripts', [
            'ts-build' => 'tsc && vite build',
        ], $this->isReset(true));

        $this->line('Archivo package.json actualizado (script "ts-build")');

        return $this;
    }

    public function modifyFile_PackageJson_toAddEngines(): self
    {
        $this->number++;

        // Add script "ts-build" in "package.json"
        $this->modifyPackageJsonSection('engines', [
            'node' => config('kalion.version_node'),
            'npm'  => config('kalion.version_npm'),
        ], $this->isReset(true));

        $this->line('Archivo package.json actualizado (engines)');

        return $this;
    }

    public function modifyFile_ComposerJson_toAddSrcNamespace(): self
    {
        $this->number++;

        // Update the "autoload.psr-4" section in "composer.json" file with additional namespaces.
        // Add the "Src" namespace into "composer.json"

        $namespaces = ['Src\\' => 'src/'];

        $filePath = base_path('composer.json');

        if (!file_exists($filePath)) {
            return $this;
        }

        $composer = json_decode(file_get_contents($filePath), true);

        if (!isset($composer['autoload']['psr-4'])) {
            $composer['autoload']['psr-4'] = [];
        }

        $psr4 = $composer['autoload']['psr-4'];

        if ($this->isReset()) {
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

    public function modifyFile_ComposerJson_toAddHelperFilePath(): self
    {
        $this->number++;

        // Ruta del archivo composer.json
        $filePath = base_path('composer.json');

        if (!file_exists($filePath)) {
            return $this;
        }

        // Cargar el contenido actual de composer.json
        $composer = json_decode(file_get_contents($filePath), true);

        if (!isset($composer['autoload'])) {
            $composer['autoload'] = [];
        }

        if (!isset($composer['autoload']['files'])) {
            $composer['autoload']['files'] = [];
        }

        $filePathToAdd = "src/Shared/Infrastructure/Helpers/helpers.php";

        if ($this->isReset()) {
            // Si estamos en modo reset, eliminamos el archivo de la lista
            $composer['autoload']['files'] = array_filter(
                $composer['autoload']['files'],
                fn($file) => $file !== $filePathToAdd
            );

            // Si la lista queda vacía, eliminamos completamente la clave "files"
            if (empty($composer['autoload']['files'])) {
                unset($composer['autoload']['files']);
            }
        } else {
            // Por defecto, agregamos el archivo si no está presente
            if (!in_array($filePathToAdd, $composer['autoload']['files'], true)) {
                $composer['autoload']['files'][] = $filePathToAdd;
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

        $action = $this->isReset() ? 'eliminado de' : 'añadido a';
        $this->line("Archivo \"helpers.php\" {$action} \"autoload.files\" en \"composer.json\"");

        return $this;
    }

    public function execute_ComposerRequire_toInstallComposerDependencies(): self
    {
        $this->number++;

        if ($this->developMode) return $this;

        // Install "tightenco/ziggy"

        $content = file_get_contents(base_path('composer.json'));

        $packages = ['tightenco/ziggy'];
        $package1 = $packages[0];

        if ($this->isReset()) {
            if (!str_contains($content, $package1)) {
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

    /*public function execute_ComposerDumpAutoload(): self
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

    public function execute_NpmInstall(): self
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

    public function execute_NpxLaravelTsUtils(): self
    {
        $this->number++;

        if ($this->isReset(true)) return $this;

        $this->execute_Process(
            ['npx', 'laravel-ts-utils'],
            'Running the "laravel-ts-utils" package start command.',
            'Laravel-ts-utils package files generated successfully.',
            'Error while generating files for the "laravel-ts-utils" package.'
        );

        return $this;
    }

    public function stubsCopyFolder_ResourcesFront(): self
    {
        $this->number++;

        // Views
        $folder = 'resources';

        if ($this->isReset(true)) return $this;

        $dir = $this->command->stubsPath($folder, true);
        $dest = base_path($folder);

        $this->filesystem->ensureDirectoryExists($dest);
        $this->filesystem->copyDirectory($dir, $dest);
        $this->line('Carpeta "'.$folder.'" creada');

        return $this;
    }

    public function execute_gitAdd(): self
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

    public function execute_NpmRunBuild(): self
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
