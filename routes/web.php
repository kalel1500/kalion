<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Thehouseofel\Kalion\Core\Infrastructure\Http\Controllers\Ajax\AjaxCheckProcessController;
use Thehouseofel\Kalion\Features\Examples\Infrastructure\Http\Controllers\Ajax\AjaxCookiesController;
use Thehouseofel\Kalion\Features\Examples\Infrastructure\Http\Controllers\Web\ExampleController;
use Thehouseofel\Kalion\Features\Jobs\Infrastructure\Http\Controllers\Ajax\AjaxJobsController;
use Thehouseofel\Kalion\Features\Jobs\Infrastructure\Http\Controllers\Web\JobsController;

Route::get('/', fn() => redirect(default_url()))->name('index');

Route::middleware('auth')->group(function () {

    // Process routes
    Route::get('/kalion/ajax/process/check-queue', [AjaxCheckProcessController::class, 'checkQueue'])->name('kalion.ajax.process.checkQueue');
    Route::get('/kalion/ajax/process/broadcast-queue-status', [AjaxCheckProcessController::class, 'broadcastQueueStatus'])->name('kalion.ajax.process.broadcastQueueStatus');
    Route::get('/kalion/ajax/process/check-reverb', [AjaxCheckProcessController::class, 'checkReverb'])->name('kalion.ajax.process.checkReverb');
    Route::get('/kalion/ajax/process/broadcast-reverb-status', [AjaxCheckProcessController::class, 'broadcastReverbStatus'])->name('kalion.ajax.process.broadcastReverbStatus');

    // Queues routes
    Route::get('/kalion/queues/jobs', [JobsController::class, 'queuedJobs'])->name('kalion.queues.queuedJobs');
    Route::get('/kalion/queues/failed-jobs', [JobsController::class, 'failedJobs'])->name('kalion.queues.failedJobs');
    Route::get('/kalion/ajax/queues/jobs', [AjaxJobsController::class, 'getJobs'])->name('kalion.ajax.queues.getJobs');
    Route::get('/kalion/ajax/queues/failed-jobs', [AjaxJobsController::class, 'getFailedJobs'])->name('kalion.ajax.queues.getFailedJobs');


    // Example routes
    Route::get('/kalion/example/example-1', [ExampleController::class, 'example1'])->name('kalion.example1');
    Route::get('/kalion/example/example-2', [ExampleController::class, 'example2'])->name('kalion.example2');
    Route::get('/kalion/example/example-3', [ExampleController::class, 'example3'])->name('kalion.example3');
    Route::get('/kalion/example/example-4', [ExampleController::class, 'example4'])->name('kalion.example4');
    Route::get('/kalion/example/compare-html', [ExampleController::class, 'compareHtml'])->name('kalion.compareHtml');
    Route::get('/kalion/example/icons', [ExampleController::class, 'icons'])->name('kalion.icons');
    Route::get('/kalion/example/modify-cookie', [ExampleController::class, 'modifyCookie'])->name('kalion.modifyCookie');
    Route::put('/kalion/ajax/cookie/update', [AjaxCookiesController::class, 'update'])->name('kalion.ajax.cookie.update');
});

require __DIR__.'/auth.php';
//require __DIR__.'/test.php';
