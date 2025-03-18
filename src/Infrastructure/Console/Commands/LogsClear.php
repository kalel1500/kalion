<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

final class LogsClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear log files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
//            exec('rm ' . storage_path('logs/*.log'));
            exec('echo "" > ' . storage_path('logs/laravel.log'));
            exec('chmod -R 777 ' . storage_path());
            Log::info('Logs limpios');
            $this->comment('Logs have been cleared!');
        } catch (Throwable $exception) {
//            $message = 'El comando "rm" no existe';
            $message = $exception->getMessage();
            Log::warning($message);
            $this->comment($message);
        }
    }
}
