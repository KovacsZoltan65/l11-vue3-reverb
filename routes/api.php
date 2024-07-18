<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/**
 * Register a new user.
 *
 * This route is responsible for handling HTTP POST requests to the '/register' endpoint.
 * It uses the 'AuthController' class and its 'register' method to handle the registration process.
 *
 * @param Request $request The HTTP request containing the user data.
 * @return \Illuminate\Http\JsonResponse The JSON response containing the user data and a success message.
 */
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
