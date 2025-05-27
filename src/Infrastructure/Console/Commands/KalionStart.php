<?php

namespace Thehouseofel\Kalion\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Thehouseofel\Kalion\Infrastructure\Services\Commands\StartCommandService;
use Thehouseofel\Kalion\Infrastructure\Traits\InteractsWithComposerPackages;

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
                    {--simple : Create only the files needed for the backend}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create starter files for kalion architecture';

    public function traitRequireComposerPackages(string $composer, array $packages, bool $isRemove = false)
    {
        $this->requireComposerPackages($composer, $packages, $isRemove);
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reset = $this->option('reset');
        $simple = $this->option('simple');

        $developString = config('kalion.package_in_develop') ? '<fg=yellow>[DEVELOP]</>' : '';
        $this->info("Inicio configuración: $developString");

        StartCommandService::configure($this, $reset, $simple)
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
            ->modifyFile_JsBootstrap_toAddImportFlowbite()
            ->modifyFile_Gitignore_toDeleteLockFileLines()
            ->modifyFile_PackageJson_toAddNpmDependencies()
            ->modifyFile_PackageJson_toAddScriptTsBuild()
            ->modifyFile_PackageJson_toAddEngines()
            ->modifyFile_ComposerJson_toAddSrcNamespace()
            ->modifyFile_ComposerJson_toAddHelperFilePath()
            ->executeComposerRequire_or_ModifyFileComposerJson_toInstallOrAddComposerDependencies()
            ->execute_ComposerDumpAutoload()
            ->execute_gitAdd()
            ->execute_NpmInstall()
            ->execute_NpmRunBuild();

        $this->info('Configuración finalizada');
    }
}
