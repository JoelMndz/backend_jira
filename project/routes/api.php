<?php

use App\Http\Controllers\AssignedUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

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

Route::controller(AuthController::class)->group(function () {
    Route::post('/auth/register', 'create');
    Route::post('/auth/login', 'login');
});

Route::middleware(['auth:sanctum'])->group(function () {

    Route::controller(ProjectController::class)->group(function () {
        Route::get('/project', 'getAll');
        Route::get('/project/{id}', 'show');
        Route::post('/project', 'create');
        Route::patch('/project/{project}', 'update');
        Route::delete('/project/{project}', 'delete');
    });

    Route::controller(AssignedUserController::class)->group(function () {
        Route::post('/assigned-user', 'create');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/user', 'getAll');
    });

    Route::get('/auth/logout', [AuthController::class, 'logout']);
});
