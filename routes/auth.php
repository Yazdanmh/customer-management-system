<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Middleware\ShareGlobalData;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest', 'throttle:10,1'])->group(function () {

    Route::get('/auth/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login')
        ->middleware(ShareGlobalData::class);

    // Route::get('register', [RegisteredUserController::class, 'create'])
    //     ->name('register');

    // Route::post('register', [RegisteredUserController::class, 'store']);

    Route::post('/auth/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/auth/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request')
        ->middleware(ShareGlobalData::class);

    Route::post('/auth/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('/auth/test', [NewPasswordController::class, 'test'])
        ->middleware(ShareGlobalData::class);

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset')
        ->middleware(ShareGlobalData::class);

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware(['auth', 'throttle:10,1'])->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/auth/confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
