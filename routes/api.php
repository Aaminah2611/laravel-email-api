<?php

use App\Http\Controllers\EmailController;
use Illuminate\Http\Request;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// 🌐 Remove /login route if Keycloak handles authentication externally

// 🔒 Protected routes using Keycloak guard (api)
Route::middleware('auth:api')->group(function () {

    // Authenticated user info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Logout: adjust according to Keycloak logout flow
    Route::post('/logout', function (Request $request) {
        // Optionally: Implement Keycloak logout redirection or token invalidation logic here
        return response()->json(['message' => 'Logged out']);
    });

    // Email send & status
    Route::post('/send-email', [EmailController::class, 'send']);
    Route::get('/emails/{id}/status', [EmailController::class, 'checkStatus']);

    // EmailTemplate CRUD
    Route::apiResource('/email-templates', EmailTemplateController::class);

    // List endpoints
    Route::get('/emails', [EmailController::class, 'listEmails']);
    Route::get('/users', [UserController::class, 'listUsers']);

    Route::delete('/emails/{id}', [EmailController::class, 'destroy']);

    // Trashed & Restore (protected)
    // Email Templates
    Route::get('/templates/trashed', [EmailTemplateController::class, 'trashed']);
    Route::post('/templates/{id}/restore', [EmailTemplateController::class, 'restore']);

    // Emails
    Route::get('/emails/trashed', [EmailController::class, 'trashed']);
    Route::post('/emails/{id}/restore', [EmailController::class, 'restore']);

    // Users
    Route::get('/users/trashed', [UserController::class, 'trashed']);
    Route::post('/users/{id}/restore', [UserController::class, 'restore']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});

// 🌐 Public test route (no change)
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});
