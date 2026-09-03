<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
<<<<<<< HEAD
=======

Route::middleware(\App\Http\Middleware\VerifyHardwareToken::class)->group(function () {
    Route::post('/hardware/lectura', [\App\Http\Controllers\Api\HardwareController::class, 'lectura']);
});
>>>>>>> 02f17e8fea3785350b13a04082ae7d35fec22650
