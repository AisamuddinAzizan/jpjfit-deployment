<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/stats', [HomeController::class, 'stats'])->name('api.stats');
Route::get('/testimonials', [HomeController::class, 'testimonials'])->name('api.testimonials');
