<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Thehouseofel\Kalion\Core\Domain\Objects\ValueObjects\Parameters\CheckableProcessVo;
use Thehouseofel\Kalion\Core\Infrastructure\Facades\ProcessChecker;

final class ProcessCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kalion:process-check
                    {process : The name of the process to be checked}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check the process specified';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isActive = ProcessChecker::isRunning(CheckableProcessVo::from($this->argument('process')));

        $text = $isActive ? 'true' : 'false';
        $this->info($text);

        return $isActive;
    }
}
