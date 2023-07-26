<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CarController;
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

// Auth
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('/car', [CarController::class, 'index']);
    Route::get('/car/main', [CarController::class, 'getMainCar']);
    Route::get('/car/{id}', [CarController::class, 'show']);
    Route::post('/car/create', [CarController::class, 'create']);
    Route::post('/car/update/{id}', [CarController::class, 'update']);
    Route::delete('/car/delete/{id}', [CarController::class, 'delete']);
});
