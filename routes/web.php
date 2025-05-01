<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Ajax\AjaxCookiesController;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Ajax\AjaxJobsController;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Ajax\AjaxQueuesController;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Ajax\AjaxWebsocketsController;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Web\ExampleController;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Web\JobsController;

Route::get('/', fn() => redirect(default_url()))->name('index');

Route::middleware('auth')->group(function () {

    // Service routes
    Route::get('/kalion/ajax/check-service-queues', [AjaxQueuesController::class, 'checkService'])->name('kalion.ajax.queues.checkService');
    Route::get('/kalion/ajax/check-service-websockets', [AjaxWebsocketsController::class, 'checkService'])->name('kalion.ajax.websockets.checkService');

    // Queues routes
    Route::get('/kalion/queues/jobs', [JobsController::class, 'queuedJobs'])->name('kalion.queues.queuedJobs');
    Route::get('/kalion/queues/failed-jobs', [JobsController::class, 'failedJobs'])->name('kalion.queues.failedJobs');
    Route::get('/kalion/ajax/queues/jobs', [AjaxJobsController::class, 'getJobs'])->name('kalion.ajax.queues.getJobs');
    Route::get('/kalion/ajax/queues/failed-jobs', [AjaxJobsController::class, 'getFailedJobs'])->name('kalion.ajax.queues.getFailedJobs');

    // Cookies routes
    Route::put('/kalion/cookie/update', [AjaxCookiesController::class, 'update'])->name('kalion.ajax.cookie.update');

    // Example routes
    Route::get('/kalion/example/example-1', [ExampleController::class, 'example1'])->name('kalion.example1');
    Route::get('/kalion/example/example-2', [ExampleController::class, 'example2'])->name('kalion.example2');
    Route::get('/kalion/example/example-3', [ExampleController::class, 'example3'])->name('kalion.example3');
    Route::get('/kalion/example/example-4', [ExampleController::class, 'example4'])->name('kalion.example4');
    Route::get('/kalion/example/compare-html', [ExampleController::class, 'compareHtml'])->name('kalion.compareHtml');
    Route::get('/kalion/example/modify-cookie', [ExampleController::class, 'modifyCookie'])->name('kalion.modifyCookie');
    Route::get('/kalion/example/icons', [ExampleController::class, 'icons'])->name('kalion.icons');
});

require __DIR__.'/auth.php';
//require __DIR__.'/test.php';
