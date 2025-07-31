<?php

use App\Http\Controllers\EmailController;
use Illuminate\Http\Request;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


// Add this to the TOP of routes/api.php (outside any middleware groups)
Route::get('/debug-routes', function () {
    return response()->json([
        'message' => 'API routes are working!',
        'timestamp' => now(),
        'url' => request()->fullUrl()
    ]);
});



// Protected routes using auth:keycloak guard middleware
Route::middleware('auth:keycloak')->group(function () {

    // Add this INSIDE the auth:keycloak middleware group
    Route::get('/debug-auth', function (Request $request) {
        Log::info('Debug-auth route hit successfully');
        return response()->json([
            'message' => 'Auth middleware passed!',
            'user' => $request->user(),
            'auth_header' => $request->header('Authorization')
        ]);
    });

    // FIXED: Removed duplicate /api prefix - this will be accessible at /api/user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Authenticated user info — the `/api/me` endpoint
    Route::get('/me', function () {
        return response()->json(Auth::user());
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

// ADD: Test route for debugging authentication (temporary)
Route::get('/test-token', function (Request $request) {
    $authHeader = $request->header('Authorization');
    
    if (!$authHeader) {
        return response()->json(['error' => 'No Authorization header'], 400);
    }
    
    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        return response()->json(['error' => 'Invalid Authorization format'], 400);
    }
    
    $token = $matches[1];
    
    // Try to decode without verification first
    $parts = explode('.', $token);
    if (count($parts) !== 3) {
        return response()->json(['error' => 'Invalid JWT format'], 400);
    }
    
    $payload = json_decode(base64_decode($parts[1]), true);
    
    return response()->json([
        'message' => 'Token parsed successfully',
        'token_length' => strlen($token),
        'payload' => $payload
    ]);
});

