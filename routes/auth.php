<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Api\Auth\NewPasswordController;
use App\Http\Controllers\Api\Auth\PasswordResetLinkController;
use App\Http\Controllers\Api\Auth\RegisteredUserController;
use App\Http\Controllers\Api\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;



Route::group(['middleware' => 'api'], function ($router) {
    Route::post('login', [AuthController::class, 'login'])->name('login');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');

    Route::post('me', [AuthController::class, 'me']);

    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('guest')
        ->name('register');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest')
        ->name('password.email');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('guest')
        ->name('password.store');

    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['auth', 'throttle:6,1'])
        ->name('verification.send');
});
