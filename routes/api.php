<?php

use App\Http\Controllers\EmailController;
use Illuminate\Http\Request;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

// Protected routes using auth:keycloak guard middleware
Route::middleware('auth:keycloak')->group(function () {

    Route::get('/api/user', function (Request $request) {
        return $request->user();
    });

    // Authenticated user info — the `/api/me` endpoint
    Route::get('/me', function () {
        return response()->json(Auth::user());
    });

    // Authenticated user info (duplicate of /me but returning raw user)
    Route::get('/user', function () {
        return Auth::user();
    });

    // Logout
    Route::post('/logout', function () {
        return response()->json(['message' => 'Logged out']);
    });

    // Email routes
    Route::post('/send-email', [EmailController::class, 'send']);
    Route::get('/emails/{id}/status', [EmailController::class, 'checkStatus']);
    Route::get('/emails', [EmailController::class, 'listEmails']);
    Route::delete('/emails/{id}', [EmailController::class, 'destroy']);
    Route::get('/emails/trashed', [EmailController::class, 'trashed']);
    Route::post('/emails/{id}/restore', [EmailController::class, 'restore']);

    // Email Template routes
    Route::apiResource('/email-templates', EmailTemplateController::class);
    Route::get('/templates/trashed', [EmailTemplateController::class, 'trashed']);
    Route::post('/templates/{id}/restore', [EmailTemplateController::class, 'restore']);

    // User routes
    Route::get('/users', [UserController::class, 'listUsers']);
    Route::get('/users/trashed', [UserController::class, 'trashed']);
    Route::post('/users/{id}/restore', [UserController::class, 'restore']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

});

// Public routes (no auth required)
Route::get('/hello', fn () => response()->json(['message' => 'hello world']));
Route::get('/test', fn () => response()->json(['message' => 'API is working']));
Route::get('/ping', fn () => response()->json(['message' => 'pong']));

Route::get('/log-test', function () {
    Log::info('✅ Log test successful');
    return 'Check your logs.';
});
