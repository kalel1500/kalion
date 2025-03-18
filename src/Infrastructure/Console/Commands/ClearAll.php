<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Infrastructure\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class ClearAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all the cache';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
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

        Artisan::call('logs:clear');

        $this->info('All cache files are cleared');
    }
}
