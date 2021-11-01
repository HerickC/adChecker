<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdController;

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

Route::get('/listAds', [AdController::class, 'listAds']);
Route::get('/uniqueId', [AdController::class, 'getAdUniqueCode']);
Route::post('/image-list', [AdController::class, 'getImageItems']);
Route::post('/set-image-list', [AdController::class, 'setImageItems']);

Route::post('/upload-image', [AdController::class, 'preProcessImage']);

Route::get('/resetAll', [AdController::class, 'resetAllAds']);
