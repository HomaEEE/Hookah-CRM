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

Route::get('/', 'App\Http\Controllers\SiteController@home');



// Route::get('/api/shift/{place_id}', 'App\Http\Controllers\PosterApiController@getCurrentShift');

// Route::get('/getSpot/{place_id}', 'App\Http\Controllers\TestController@getSpot');

    Route::prefix('{suffix?}')->group(function () {
        Route::get('/', 'App\Http\Controllers\SiteController@index');
        Route::get('/menu', 'App\Http\Controllers\SiteController@menu');
        Route::get('/vacancy', 'App\Http\Controllers\SiteController@vacancy');
        Route::get('/booking', 'App\Http\Controllers\SiteController@booking');
        Route::get('/feedbacks', 'App\Http\Controllers\SiteController@feedbacks');
        Route::get('/contacts', 'App\Http\Controllers\SiteController@contacts');
    });


Route::post('/sendFeedback', 'App\Http\Controllers\SiteController@sendFeedback');
Route::post('/sendReserve', 'App\Http\Controllers\SiteController@sendReserve');


Route::get('lang/{lang}',
    ['as' => 'lang.switch', 'uses' => 'App\Http\Controllers\SiteController@switchLang']);