<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class ClearAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kalion:clear-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all the cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /*Artisan::call('schedule:clear-cache');
        Artisan::call('debugbar:clear');*/

        /*Artisan::call('cache:clear');
        Artisan::call('clear-compiled');
        Artisan::call('config:clear');
        Artisan::call('event:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');*/
        Artisan::call('optimize:clear');

        Artisan::call('kalion:logs-clear');

        $this->info('All cache files are cleared');
    }
}
