<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\StripeController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\SupportController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\UserController;
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

Route::get('/success', [StripeController::class, 'successPayment']);
Route::get('/failed', [StripeController::class, 'failedPayment']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('user/image/{user}', [UserController::class, 'getFile'])->name('user.image');
Route::get('file/file/{file}', [FileController::class, 'getFile'])->name('file.file');
Route::post('/reset_password', [UserController::class, 'resetPassword']);

Route::middleware('auth:api')->group(function () {
    Route::apiResource('supports', SupportController::class);
    Route::apiResource('users', UserController::class)->only(['store', 'update']);
    Route::get('/me', [AuthController::class, 'user']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/users_filter', [UserController::class, 'userFilter']);
    Route::group(['middleware' => ['can:index.users']], function () {
        Route::resource('users', UserController::class)->only('index');
    });
    Route::group(['middleware' => ['can:destroy.users']], function () {
        Route::resource('users', UserController::class)->only('destroy');
    });
    Route::get('/update_auth_user_space', [FileController::class, 'updateAuthUserSpace']);

    Route::group(['middleware' => ['can:plans']], function () {
        Route::apiResource('plans', PlanController::class);
    });

    Route::apiResource('folders', FolderController::class);
    Route::get('/user_folders', [FolderController::class, 'getUserFolders']);

    Route::apiResource('files', FileController::class);
    Route::post('/file_filter', [FileController::class, 'fileFilter']);
    Route::get('/user_files/{folder}', [FileController::class, 'getUserFiles']);

    Route::group(['middleware' => ['can:supports']], function () {
        Route::resource('supports', SupportController::class)->only('index');
    });

    Route::group(['middleware' => ['can:topics']], function () {
        Route::resource('topics', TopicController::class);
    });

    Route::post('/bill/{user}', [StripeController::class, 'bill']);
});
