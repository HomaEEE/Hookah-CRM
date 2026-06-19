<?php

use Illuminate\Support\Facades\Route;
use Modules\Cms\App\Http\Controllers\ApiController;

Route::prefix('v1')->group(function () {
    // Маршрут для создания резерва
    Route::post('/reservations', [ApiController::class, 'reservation']);

    // Маршрут для создания отзыва
    Route::post('/reviews', [ApiController::class, 'review']);
});
