<?php

use App\Http\Controllers\EmailController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailTemplateController;


// Public login route to issue tokens
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
});

// 🔒 Protected routes that require valid Sanctum token
Route::middleware('auth:sanctum')->group(function () {

    // ✅ Protected: Get authenticated user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ✅ Protected: Logout and revoke token
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    });

    // ✅ Protected: Email send
    Route::post('/send-email', [EmailController::class, 'send']);

    // ✅ Protected: Email status query
    Route::get('/emails/{id}/status', [EmailController::class, 'checkStatus']);

    // ✅ Protected: EmailTemplate CRUD
    Route::apiResource('/email-templates', EmailTemplateController::class);
});


// 🌐 Public test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});
