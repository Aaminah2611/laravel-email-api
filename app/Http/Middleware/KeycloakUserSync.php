<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;

class KeycloakUserSync
{
    public function handle(Request $request, Closure $next)
{
    $authHeader = $request->header('Authorization');

    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        return response()->json(['error' => 'Unauthorized: No token'], 401);
    }

    $tokenString = $matches[1];

    try {
        $decoded = JWT::decode($tokenString, new \Firebase\JWT\Key('MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwFXQ8TQN9VRUFARO0wMTEKaflXfPXoDvF3Ona5CSj+Cv2d/Z51XouZcMf1VoKySAS3sf9DhWkjPzo4ydOmjsHpqmToFnNwFshB9CtspwmHVESDyxKD08JOEW6wMJBi6qAGiY393BZPXUmfpnRURQ67MVlY3sf2yzbcoU+7tiL5VXM+kZjcRLZlSuciB7flgf6icYU7QvMg5yyGOQ3Mb8RU6tToVlpY+vrUgyWXYBLRDW4qbXZRv9VyqMozfrBrn8jADhNbcZykPE2vEg1Ch9wvTl93QIhW/JK2GmJTmJAeuO131I5TPC+d+OXJgJUFoVurJn1Fpf4rWDggIHSqU5pwIDAQAB', 'RS256'));

        $keycloakId = $decoded->sub ?? null;

        if (!$keycloakId) {
            return response()->json(['error' => 'Unauthorized: Invalid token payload'], 401);
        }

        $user = User::firstOrCreate(
            ['keycloak_id' => $keycloakId],
            [
                'email' => $decoded->email ?? null,
                'name' => $decoded->name ?? $decoded->preferred_username ?? 'No Name',
                'password' => bcrypt(str()->random(16)),
                'email_verified_at' => now(),
            ]
        );

        Auth::setUser($user);

    } catch (\Exception $e) {
        Log::warning('Keycloak token invalid: '.$e->getMessage());
        return response()->json(['error' => 'Unauthorized: Token invalid'], 401);
    }

    return $next($request);
}

}
