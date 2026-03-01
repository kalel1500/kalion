<?php

namespace Thehouseofel\Kalion\Core\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Thehouseofel\Kalion\Core\Infrastructure\Console\Commands\Concerns\InteractsWithComposerPackages;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\Objects\InstallDto;
use Thehouseofel\Kalion\Core\Infrastructure\Support\Install\ProcessorBuilder;

class Install extends Command
{
    use InteractsWithComposerPackages;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kalion:install
                    {--composer=global : Absolute path to the Composer binary which should be used to install packages}
                    {--step= : Select a specific step to execute}
                    {--reset : Reset all changes made by the command to the original state}
                    {--with-examples : Generate the example files}';

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
        $stepsPath          = normalize_path(KALION_PATH . '/install/steps/');
        $stepsPattern       = $stepsPath . '*.php';

        $data = new InstallDto(
            reset             : $this->option('reset'),
            withExamples      : $this->option('with-examples'),
            developMode       : config('kalion.command.start.package_in_develop'),
            keepMigrationsDate: config('kalion.command.start.keep_migrations_date'),
            pattern           : $stepsPattern,
            pathGenBase       : KALION_PATH . '/install/stubs/generate/base',
            pathGenExamples   : KALION_PATH . '/install/stubs/generate/examples',
            pathOriginal      : KALION_PATH . '/install/stubs/original',
            selectedStep      : $this->getSelectedStep($this->option('step'), $stepsPattern, $stepsPath),
        );

        $this->info('Inicio configuración: ' . ($data->developMode ? '<fg=yellow>[DEVELOP]</>' : ''));

        (new ProcessorBuilder())
            ->withCommand($this)
            ->withData($data)
            ->build()
            ->execute();

        $this->info('Configuración finalizada');
    }

    protected function getSelectedStep(?string $stepName, string $stepsPattern, string $stepsPath): ?string
    {
        if (!$stepName) {
            return null;
        }

        $steps       = [];
        $stepsPretty = [];
        $files       = glob($stepsPattern);
        foreach ($files as $file) {
            $name = str_replace($stepsPath, '', $file);
            if (str_contains($name, $stepName)) {
                $steps[$name]  = $file;
                $stepsPretty[] = $name;
            }
        }

        if (empty($steps)) {
            $this->warn(__('k::error.step_not_found_$name', ['name' => $stepName]));
            return null;
        };

        $selected = (count($steps) > 1)
            ? $this->choice(__('k::error.multiple_steps_found_$name', ['name' => $stepName]), $stepsPretty)
            : $stepsPretty[0];

        if (is_null($selected)) {
            $this->warn(__('no_step_selected'));
            return null;
        }

        return $steps[$selected];
    }
}
