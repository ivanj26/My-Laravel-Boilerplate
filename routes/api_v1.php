<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Captcha\CaptchaController;
use App\Http\Controllers\Api\Document\DocumentController;
use App\Http\Controllers\Api\Notification\NotificationController;

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

    // Auth service
    Route::prefix('session')->group(function() {
        Route::post('create', [AuthController::class, 'createSession']);
        Route::post('sign-up', [AuthController::class, 'signUp']);
    });

    // User service
    Route::prefix('user')->group(function(){
        Route::post('profile/verify/{token}', [AuthController::class, 'verifyEmail']);
    });

    Route::get('captcha/refresh', [CaptchaController::class, 'refreshCaptcha']);

    // Document service
    Route::prefix('documents')->group(function() {
        Route::get('types', [DocumentController::class, 'types']);
        Route::get('{fileName}', [DocumentController::class, 'getByFilename']);
    });

    /**
     * PRIVATE APIs
     */
    Route::middleware(['auth:sanctum'])
        ->group(function () {
            Route::post('session/revoke', [AuthController::class, 'revokeSession']);

            // Notification service
            Route::prefix('notifications')->group(function() {
                Route::post('email/send', [NotificationController::class, 'emailSend']);
                Route::post('sms/send', [NotificationController::class, 'smsSend']);
            });

            // Document service (for storing data)
            Route::post('documents', [DocumentController::class, 'store']);

            // Other service
            //
        });
});
