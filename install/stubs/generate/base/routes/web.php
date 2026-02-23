<?php

use Illuminate\Support\Facades\Route;

Route::get('/welcome', fn() => view('welcome'))->name('welcome');

// Protected routes
Route::middleware(['auth'])->group(function () {
    //
});
