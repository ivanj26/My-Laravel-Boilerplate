<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Category\CategoryController;

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

// API
Route::group([
    'as' => 'api.v1.',
], function () {
    /**
     * PUBLIC APIs
     */
    Route::get('/', [IndexController::class, 'healthCheck']);

    Route::post('session/create', [AuthController::class, 'createSession']);
    Route::post('session/sign-up', [AuthController::class, 'signUp']);

    /**
     * PRIVATE APIs
     */
    Route::middleware(['auth:sanctum'])
        ->group(function () {
            Route::post('session/revoke', [AuthController::class, 'revokeSession']);

            // Other service
            //
        });
});
