<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;

Route::group([
    'as' => 'api.v1.admin'
], function () {
    /**
     * PUBLIC APIs
    */
    Route::post('session/create', [AuthController::class, 'createSession']);

    /**
     * PRIVATE APIs
     */
    Route::middleware(['auth:sanctum', 'admin'])
        ->group(function () {
            Route::post('session/revoke', [AuthController::class, 'revokeSession']);

            // Other service
            //
        });
});