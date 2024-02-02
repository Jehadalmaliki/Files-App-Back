<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\FileFolderController;
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
    Route::get('/files', [FileController::class, 'index']);
    Route::put('/files/update/{id}', [FileController::class, 'update']);
    Route::delete('/files/delete/{id}', [FileController::class, 'delete']);


    Route::get('/folders/{id}/contents', [FolderController::class, 'getContents']);
   
    Route::delete('/folders/{id}', [FolderController::class, 'delete']);



    Route::post('/folders/create', [FolderController::class, 'create']);
    Route::post('/folders/{folderId}/upload', [FolderController::class, 'uploadFile']);

    Route::get('/folders/{id}', [FolderController::class, 'show']);
