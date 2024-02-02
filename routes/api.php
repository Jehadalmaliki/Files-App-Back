<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\FileManagerController;
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

    Route::post('/files/upload', [FileController::class, 'upload']);
    Route::get('/files/show/{id}', [FileController::class, 'show']);
    Route::get('/files/index', [FileController::class, 'index']);
    Route::put('/files/update/{id}', [FileController::class, 'update']);
    Route::delete('/files/delete/{id}', [FileController::class, 'delete']);


    



