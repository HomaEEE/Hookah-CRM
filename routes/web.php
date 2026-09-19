<?php

use App\Http\Controllers\PrintController;
use Illuminate\Support\Facades\Artisan;
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

// Route::prefix('bot')->group(function () {
//     Route::get('/', 'App\Http\Controllers\TelegramBotController@index')->name('bot.index');
//     Route::get('hookah', 'App\Http\Controllers\TelegramBotController@hookah')->name('bot.hookah');
//     Route::get('waiter', 'App\Http\Controllers\TelegramBotController@waiter')->name('bot.waiter');
//     Route::get('bill', 'App\Http\Controllers\TelegramBotController@bill')->name('bot.bill');
//     Route::post('order', 'App\Http\Controllers\TelegramBotController@order')->name('bot.order');
// });

Route::passkeys();

Route::middleware(['web'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/reports/cashflow/download', [PrintController::class, 'ReportCashflow'])
            ->name('cashflow.report.pdf');
    });

Route::get('/manifest-admin.json', function () {
    $manifestPath = public_path('manifest-admin.json');
    if (! file_exists($manifestPath)) {
        Artisan::call('pwa:manifest');
    }

    return response()->file($manifestPath, [
        'Content-Type' => 'application/manifest+json',
        'Cache-Control' => 'no-cache, private',
    ]);
})->name('manifest.admin');
