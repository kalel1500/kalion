<?php

namespace Thehouseofel\Kalion\Core\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Thehouseofel\Kalion\Core\Infrastructure\Console\Commands\Concerns\InteractsWithComposerPackages;
use Thehouseofel\Kalion\Core\Infrastructure\Services\Commands\StartCommandService;

class KalionStart extends Command
{
    use InteractsWithComposerPackages;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kalion:start
                    {--composer=global : Absolute path to the Composer binary which should be used to install packages}
                    {--reset : Reset all changes made by the command to the original state}
                    {--skip-examples : Don\'t generate the example files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create starter files for kalion architecture';

    /**
     * @throws \RuntimeException
     * @throws \LogicException
     */
    public function traitRequireComposerPackages(string $composer, array $packages, bool $isRemove = false): bool
    {
        return $this->requireComposerPackages($composer, $packages, $isRemove);
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reset        = $this->option('reset');
        $skipExamples = $this->option('skip-examples');

        $developString = config('kalion.package_in_develop') ? '<fg=yellow>[DEVELOP]</>' : '';
        $this->info("Inicio configuración: $developString");

        StartCommandService::configure($this, $reset, $skipExamples)
            ->publishKalionConfig()
            ->stubsCopyFile_DependencyServiceProvider()
            ->stubsCopyFiles_Config()
            ->stubsCopyFiles_Migrations()
            ->stubsCopyFiles_Js()
            ->stubsCopyFolder_Factories()
            ->stubsCopyFolder_Seeders()
            ->stubsCopyFolder_Lang()
            ->stubsCopyFolder_Resources()
            ->stubsCopyFolder_Src()
            ->stubsCopyFile_RoutesWeb()
            ->createEnvFiles()
            ->deleteDirectory_Http()
            ->deleteDirectory_Models()
            ->deleteFile_Changelog()
            ->modifyFile_BootstrapProviders_toAddDependencyServiceProvider()
            ->modifyFile_BootstrapApp_toAddMiddlewareRedirect()
            ->modifyFile_BootstrapApp_toAddExceptionHandler()
            ->modifyFile_ConfigApp_toUpdateTimezone()
            ->modifyFile_Gitignore_toDeleteLockFileLines()
            ->modifyFile_PackageJson_toAddNpmDependencies()
            ->modifyFile_PackageJson_toAddScriptTsBuild()
            ->modifyFile_PackageJson_toAddEngines()
            ->modifyFile_ComposerJson_toAddSrcNamespace()
            ->modifyFile_ComposerJson_toAddHelperFilePath()
            ->execute_ComposerRequire_toInstallComposerDependencies()
            ->execute_ComposerDumpAutoload()
            ->execute_gitAdd()
            ->execute_NpmInstall()
            ->execute_NpmRunBuild();

        $this->info('Configuración finalizada');
    }
}
