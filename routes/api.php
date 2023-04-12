<?php

use App\Http\Controllers\API\FileUploadController;
use App\Http\Controllers\API\StatusController;
use App\Http\Controllers\API\TaskController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\TagController;
use Illuminate\Http\Request;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('registration', [UserController::class, 'registration']);
Route::post('login', [UserController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('task', TaskController::class);
    Route::apiResource('file-upload', FileUploadController::class);
    Route::apiResource('tag', TagController::class);
    Route::get('logout', [UserController::class, 'logout']);
    Route::get('user', [UserController::class, 'user']);
    Route::put('changeStatus/{task}', [TaskController::class, 'changeStatus']);
    Route::put('archive/{task}', [TaskController::class, 'archiveTask']);
    Route::post('filter-tasks', [TaskController::class, 'filterTasks']);
    Route::post('sort-tasks', [TaskController::class, 'sortTasks']);
});
