<?php

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

Route::controller(App\Http\Controllers\AuthController::class)->group(function(){
    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('login'); 
});

Route::controller(App\Http\Controllers\ProjectController::class)->group(function(){

    Route::post('/projects', 'store');
    Route::put('/projects', 'update');
    Route::get('/projects', 'index');
    Route::post('/projects/pinned', 'pinnedProject');
    
});

Route::controller(\App\Http\Controllers\MemberController::class)->group(function(){
    Route::post('/members', 'store');
    Route::put('/members', 'update');
    Route::get('/members', 'index');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
