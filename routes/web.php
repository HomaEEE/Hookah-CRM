<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', 'App\Http\Controllers\SiteController@index');
// Route::get('/api/shift/{place_id}', 'App\Http\Controllers\PosterApiController@getCurrentShift');

// Route::get('/getSpot/{place_id}', 'App\Http\Controllers\TestController@getSpot');

//   Route::prefix('{suffix?}')->group(function () {
// Route::get('/menu', 'App\Http\Controllers\SiteController@menu');
Route::get('/seet/{seet?}', 'App\Http\Controllers\SiteController@seet');

Route::get('/menu/{seet?}', 'App\Http\Controllers\SiteController@menu');
Route::get('/vacancy', 'App\Http\Controllers\SiteController@vacancy');
Route::get('/rules', 'App\Http\Controllers\SiteController@rules');
Route::get('/booking', 'App\Http\Controllers\SiteController@booking');
Route::get('/feedbacks', 'App\Http\Controllers\SiteController@feedbacks');
Route::get('/contacts', 'App\Http\Controllers\SiteController@contacts');
// });

Route::post('/sendFeedback', 'App\Http\Controllers\SiteController@sendFeedback');
Route::post('/sendReserve', 'App\Http\Controllers\SiteController@sendReserve');

Route::prefix('bot')->group(function () {
    Route::get('/', 'App\Http\Controllers\TelegramBotController@index')->name('bot.index');
    Route::get('hookah', 'App\Http\Controllers\TelegramBotController@hookah')->name('bot.hookah');
    Route::get('waiter', 'App\Http\Controllers\TelegramBotController@waiter')->name('bot.waiter');
    Route::get('bill', 'App\Http\Controllers\TelegramBotController@bill')->name('bot.bill');
    Route::post('order', 'App\Http\Controllers\TelegramBotController@order')->name('bot.order');
});

Route::get('lang/{lang}',
    ['as' => 'lang.switch', 'uses' => 'App\Http\Controllers\SiteController@switchLang']);

Route::middleware(['web'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/reports/cashflow/download', [\App\Http\Controllers\PrintController::class, 'ReportCashflow'])
            ->name('cashflow.report.pdf');
    });

// Route::get('/reports/cashflow', [\App\Http\Controllers\PrintController::class, 'PreviewCashflow'])
//     ->name('cashflow.report.preview');
