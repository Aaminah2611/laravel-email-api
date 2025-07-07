<?php
use Illuminate\Support\Facades\Route;

Route::get('/test-db', function () {
    try {
        $results = DB::select('SELECT sqlite_version() as version');
        return response()->json($results);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});
