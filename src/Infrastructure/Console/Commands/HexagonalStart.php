<?php

namespace Thehouseofel\Hexagonal\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Process\Process;
use Thehouseofel\Hexagonal\Infrastructure\Services\StartCommandService;
use Thehouseofel\Hexagonal\Infrastructure\Traits\InteractsWithComposerPackages;
use function Illuminate\Filesystem\join_paths;

class HexagonalStart extends Command
{
    use InteractsWithComposerPackages;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hexagonal:start
                    {--composer=global : Absolute path to the Composer binary which should be used to install packages}
                    {--reset : Reset all changes made by the command to the original state}
                    {--simple : Create only the files needed for the backend}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create starter files for hexagonal architecture';

    protected $filesystem;
    protected $stubsPath;
    protected $stubsPathFront;
    protected $originalStubsPath;

    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $stubsBasePath           = HEXAGONAL_PATH . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR;
        $this->filesystem        = $filesystem;
        $this->stubsPath         = $stubsBasePath . 'generate' . DIRECTORY_SEPARATOR . 'simple';
        $this->stubsPathFront    = $stubsBasePath . 'generate' . DIRECTORY_SEPARATOR . 'front';
        $this->originalStubsPath = $stubsBasePath . 'original';
    }

    public function filesystem(): Filesystem
    {
        return $this->filesystem;
    }

    public function stubsPath($path = '', $isFront = false): string
    {
        $stubsPath = $isFront ? $this->stubsPathFront : $this->stubsPath;
        return join_paths($stubsPath, $path);
    }

    public function originalStubsPath($path = ''): string
    {
        return join_paths($this->originalStubsPath, $path);
    }

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

        $developString = config('hexagonal.package_in_develop') ? '<fg=yellow>[DEVELOP]</>' : '';
        $this->info("Inicio configuración: $developString");

        StartCommandService::configure($this, $reset, $simple)
            ->restoreFilesModifiedByPackageLaravelTsUtils()
            ->publishHexagonalConfig()
            ->stubsCopyFile_AppServiceProvider()
            ->stubsCopyFile_DependencyServiceProvider()
            ->stubsCopyFiles_Config()
            ->stubsCopyFiles_Migrations()
            ->stubsCopyFolder_Factories()
            ->stubsCopyFolder_Seeders()
            ->stubsCopyFolder_Lang()
            ->stubsCopyFolder_Resources()
            ->stubsCopyFolder_Src()
            ->stubsCopyFile_RoutesWeb()
            ->stubsCopyFile_tailwindConfigJs()
            ->createEnvFiles()
            ->deleteDirectory_Http()
            ->deleteDirectory_Models()
            ->deleteFile_Changelog()
            ->modifyFile_BootstrapProviders_toAddDependencyServiceProvider()
            ->modifyFile_BootstrapApp_toAddMiddlewareRedirect()
            ->modifyFile_BootstrapApp_toAddExceptionHandler()
            ->modifyFile_ConfigAuth_toUpdateModel()
            ->modifyFile_JsBootstrap_toAddImportFlowbite()
            ->modifyFile_Gitignore_toDeleteLockFileLines()
            ->modifyFile_PackageJson_toAddNpmDevDependencies()
            ->modifyFile_PackageJson_toAddNpmDependencies()
            ->modifyFile_PackageJson_toAddScriptTsBuild()
            ->modifyFile_ComposerJson_toAddSrcNamespace()
            ->modifyFile_ComposerJson_toAddHelperFilePath()
            ->execute_ComposerRequire_toInstallComposerDependencies()
            ->execute_NpmInstall()
            ->execute_NpxLaravelTsUtils()
            ->execute_gitAdd()
            ->execute_NpmRunBuild();

        $this->info('Configuración finalizada');

    }
}
