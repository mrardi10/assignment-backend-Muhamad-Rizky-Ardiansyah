<?php

use App\Http\Controllers\UserController;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group([
    'middleware' => 'api'
], function ($router) {

    Route::group([
        'prefix' => 'auth'
    ], function ($router) {
        Route::post('login', [UserController::class, 'login']);
        Route::post('signup', [UserController::class, 'signup']);
    });

    Route::group([
        'middleware' => 'auth:api',
        'prefix' => 'user'
    ], function ($router) {
        Route::get('userlist', [UserController::class, 'index']);
    });
});
