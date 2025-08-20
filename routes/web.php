<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/test-db', function () {
    try {
        $results = DB::select('SELECT sqlite_version() as version');
        return response()->json($results);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

use Illuminate\Support\Facades\Log;

Route::get('/env-debug', function () {
    Log::info('Env debug:', [
        'MAILTRAP_INBOX_ID' => env('MAILTRAP_INBOX_ID'),
        'MAILTRAP_API_TOKEN' => env('MAILTRAP_API_TOKEN'),
    ]);
    return response()->json([
        'MAILTRAP_INBOX_ID' => env('MAILTRAP_INBOX_ID'),
        'MAILTRAP_API_TOKEN' => env('MAILTRAP_API_TOKEN'),
    ]);
});

// Route::get('/', function() {
//     return 'Laravel API is running!';
// });


