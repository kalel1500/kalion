<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Thehouseofel\Kalion\Infrastructure\Services\Commands\PublishAuthCommandService;

final class PublishAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kalion:publish-auth {--reset : Reset all changes made by the command to the original state}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the "config/auh" file to match the package configuration so that it can be modified.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reset = $this->option('reset');

        $developString = config('kalion.package_in_develop') ? '<fg=yellow>[DEVELOP]</>' : '';
        $this->info("Inicio configuración: $developString");

        PublishAuthCommandService::configure($this, $reset)
            ->publishConfigKalionUser()
            ->modifyFile_ConfigAuth_toUpdateModelAndAddApi();

    }
}
