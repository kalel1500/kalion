<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Thehouseofel\Kalion\Infrastructure\Http\Controllers\Web\TestController;

Route::get('/kalion/test/sessions',  [TestController::class, 'sessions']);