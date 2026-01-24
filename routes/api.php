<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\MeController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\AvailableCarsController;

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

Route::prefix('auth')->group(function () {
    Route::post('/register', RegisterController::class)->name('api.auth.register');
    Route::post('/login', LoginController::class)->name('api.auth.login');
});

Route::middleware(['auth:sanctum', 'corporate.role'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('/me', MeController::class)->name('api.auth.me');
        Route::post('/logout', LogoutController::class)->name('api.auth.logout');
    });

    Route::post('/available-cars', AvailableCarsController::class)
        ->name('api.available-cars');
});
