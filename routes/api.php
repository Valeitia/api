<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/welcome', 'Controller@welcome');

Route::prefix('/auth')->group(function() {
    Route::post('/create', 'AuthController@create');
});

Route::prefix('/inventory')->group(function() {
    Route::post('/add', 'InventoryController@add');
    Route::post('/remove', 'InventoryController@remove');
    Route::post('/equip', 'InventoryController@equip');
});

Route::prefix('/battle')->group(function () {
   Route::post('/init', 'BattleController@init');
});

Route::prefix('/user')->group(function() {
    Route::post('/profile', 'UserController@profile');
});
