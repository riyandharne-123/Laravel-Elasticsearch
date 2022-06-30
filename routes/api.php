<?php

use Illuminate\Support\Facades\Route;

//controllers
use App\Http\Controllers\PostController;

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


Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);
Route::post('/posts/create', [PostController::class, 'store']);
Route::put('/posts/update/{id}', [PostController::class, 'update']);
Route::delete('/posts/delete/{id}', [PostController::class, 'destroy']);
