
<?php

use Illuminate\Support\Facades\Route;
use Modules\Cms\Http\Controllers\SiteController;

Route::get('/', [SiteController::class, 'index'])->name('home');
// Route::prefix('{suffix?}')->group(function () {

Route::get('/seet/{seet?}', [SiteController::class, 'seet'])->name('seet');
Route::get('/menu/{seet?}', [SiteController::class, 'menu'])->name('menu');
Route::get('/vacancy', [SiteController::class, 'vacancy'])->name('vacancy');
Route::get('/rules', [SiteController::class, 'rules'])->name('rules');
Route::get('/booking', [SiteController::class, 'booking'])->name('booking');
Route::get('/feedbacks', [SiteController::class, 'feedbacks'])->name('feedbacks');
Route::get('/contacts', [SiteController::class, 'contacts'])->name('contacts');
// });

Route::post('/sendFeedback', 'App\Http\Controllers\SiteController@sendFeedback');
Route::post('/sendReserve', 'App\Http\Controllers\SiteController@sendReserve');

Route::get('/lang/{lang}', fn () => redirect()->back())->name('lang.switch');
