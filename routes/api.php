<?php

require __DIR__.'/auth.php';

use App\Http\Controllers\Api\AnimalController;
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

Route::group(['middleware' => 'api'], function ($router) {
    Route::apiResource('animals', AnimalController::class, ['except' => ['update']]);
    Route::patch('animals/{animal}', [AnimalController::class, 'update'])->name('animals.update');
});
