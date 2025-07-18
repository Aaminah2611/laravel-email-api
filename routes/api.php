<?php

use App\Http\Controllers\EmailController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\UserController;

// 🌐 Public login route to issue tokens

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json(['token' => $token]);
})->name('login');


// 🔒 Protected routes that require valid Sanctum token
Route::middleware('auth:sanctum')->group(function () {

    // ✅ Authenticated user info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ✅ Logout
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    });

    // ✅ Email send & status
    Route::post('/send-email', [EmailController::class, 'send']);
    Route::get('/emails/{id}/status', [EmailController::class, 'checkStatus']);

    // ✅ EmailTemplate CRUD
    Route::apiResource('/email-templates', EmailTemplateController::class);

    // ✅ List endpoints
    Route::get('/emails', [EmailController::class, 'listEmails']);
    Route::get('/users', [UserController::class, 'listUsers']);

    Route::delete('/emails/{id}', [EmailController::class, 'destroy']);


    // ✅ Trashed & Restore (protected)
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

// 🌐 Public test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});
