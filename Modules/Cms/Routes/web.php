
<?php

use Illuminate\Support\Facades\Route;
use Modules\Cms\app\Http\Controllers\SiteController;

Route::get('/', [SiteController::class, 'index'])->name('index');

Route::get('/lang/{lang}', fn () => redirect()->back())->name('lang.switch');

Route::get('/seet/{seet?}', [SiteController::class, 'seet'])->name('seet');
Route::get('/menu/{seet?}', [SiteController::class, 'menu'])->name('menu');
Route::get('/vacancy', [SiteController::class, 'vacancy'])->name('vacancy');
Route::get('/rules', [SiteController::class, 'rules'])->name('rules');
Route::get('/booking', [SiteController::class, 'booking'])->name('booking');
Route::get('/feedbacks', [SiteController::class, 'feedbacks'])->name('feedbacks');

Route::post('/sendFeedback', [SiteController::class, 'sendFeedback']);
Route::post('/sendReserve', [SiteController::class, 'sendReserve']);
