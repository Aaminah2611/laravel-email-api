<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
public function redirectTo($request)
{
    // For API requests, return JSON 401 instead of redirect
    if ($request->expectsJson()) {
        abort(response()->json(['message' => 'Unauthenticated.'], 401));
    }

    // For web requests, you can redirect to Keycloak login URL or show error
    // Replace with your actual Keycloak login URL or route
    return config('keycloak.login_url') ?? 'https://your-keycloak-server/auth/realms/yourrealm/protocol/openid-connect/auth';
}




}
