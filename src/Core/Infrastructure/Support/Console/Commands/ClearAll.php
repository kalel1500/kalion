<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Support\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearAll extends Command
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
        Artisan::call('optimize:clear');
//        Artisan::call('config:clear');
//        Artisan::call('cache:clear');
//        Artisan::call('clear-compiled');
//        Artisan::call('event:clear');
//        Artisan::call('route:clear');
//        Artisan::call('view:clear');

//        Artisan::call('debugbar:clear');

        Artisan::call('kalion:logs-clear');

//        Artisan::call('schedule:clear-cache');

        $this->info('All cache files are cleared');
    }
}
