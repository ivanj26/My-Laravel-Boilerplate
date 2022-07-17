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

    // Document service
    Route::prefix('documents')->group(function() {
        Route::get('types', [DocumentController::class, 'types']);
        Route::get('{fileName}', [DocumentController::class, 'getByFilename']);
    });

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
