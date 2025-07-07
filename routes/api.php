<?php

use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Route;

Route::post('/send-email', [EmailController::class, 'send']);

Route::get('/test', function () {
    return response()->json(['message' => 'API is working']);
});
