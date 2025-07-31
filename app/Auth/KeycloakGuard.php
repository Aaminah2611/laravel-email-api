<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Auth\CustomUserProvider;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class KeycloakGuard implements Guard
{
    protected $user;
    protected $provider;
    protected $request;

    public function __construct(UserProvider $provider, Request $request)
    {
        $this->provider = $provider;
        $this->request = $request;
    }

    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        $authHeader = $this->request->header('Authorization');
        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            Log::info('KeycloakGuard: No valid Authorization header');
            return null;
        }

        $tokenString = $matches[1];

        // Get public key from config
        $publicKeyString = config('keycloak.realm_public_key');
        if (!$publicKeyString) {
            Log::error('KeycloakGuard: No public key configured');
            return null;
        }

        // Format the public key properly
        $publicKey = "-----BEGIN PUBLIC KEY-----\n" . 
                    chunk_split($publicKeyString, 64, "\n") . 
                    "-----END PUBLIC KEY-----";

        try {
            $algorithm = config('keycloak.algorithms', 'RS256');
            $decoded = JWT::decode($tokenString, new Key($publicKey, $algorithm));

            Log::info('KeycloakGuard: Token decoded successfully', [
                'sub' => $decoded->sub ?? 'missing',
                'email' => $decoded->email ?? 'missing',
                'name' => $decoded->name ?? 'missing',
                'preferred_username' => $decoded->preferred_username ?? 'missing'
            ]);

            $keycloakId = $decoded->sub ?? null;
            if (!$keycloakId) {
                Log::warning('KeycloakGuard: No subject in token');
                return null;
            }

            // Try to find existing user
            $user = null;
            if (method_exists($this->provider, 'findByKeycloakIdOrEmail')) {
                /** @var CustomUserProvider $customProvider */
                $customProvider = $this->provider;
                $user = $customProvider->findByKeycloakIdOrEmail(
                    $keycloakId,
                    $decoded->email ?? null
                );
            } else {
                $user = $this->provider->retrieveByCredentials(['keycloak_id' => $keycloakId]);
            }

            // If user doesn't exist, create them automatically
            if (!$user) {
                Log::info('KeycloakGuard: User not found in database, creating new user', [
                    'keycloak_id' => $keycloakId,
                    'email' => $decoded->email ?? null,
                    'name' => $decoded->name ?? $decoded->preferred_username ?? 'Unknown'
                ]);
                
                try {
                    $user = $this->createUserFromToken($decoded);
                    Log::info('KeycloakGuard: User created successfully', [
                        'user_id' => $user->id,
                        'keycloak_id' => $user->keycloak_id,
                        'email' => $user->email
                    ]);
                } catch (\Exception $e) {
                    Log::error('KeycloakGuard: Failed to create user', [
                        'error' => $e->getMessage(),
                        'keycloak_id' => $keycloakId,
                        'email' => $decoded->email ?? null
                    ]);
                    return null;
                }
            }

            $this->user = $user;
            Log::info('KeycloakGuard: User authenticated successfully', [
                'user_id' => $user->id,
                'keycloak_id' => $user->keycloak_id
            ]);
            return $this->user;
            
        } catch (\Exception $e) {
            Log::error('KeycloakGuard: Token validation failed', [
                'error' => $e->getMessage(),
                'token_length' => strlen($tokenString),
                'class' => get_class($e)
            ]);
            return null;
        }
    }

    /**
     * Create a new user from Keycloak token data
     */
    protected function createUserFromToken($decoded)
    {
        $userData = [
            'keycloak_id' => $decoded->sub,
            'email' => $decoded->email ?? null,
            'name' => $this->extractName($decoded),
            'email_verified_at' => $this->extractEmailVerification($decoded),
            'password' => null, // Keycloak users don't need local passwords
        ];

        // Remove null values except for password (which should be explicitly null)
        $userData = array_filter($userData, function($value, $key) {
            return $value !== null || $key === 'password';
        }, ARRAY_FILTER_USE_BOTH);

        return User::create($userData);
    }

    /**
     * Extract user's name from token
     */
    protected function extractName($decoded)
    {
        // Try different name fields in order of preference
        if (!empty($decoded->name)) {
            return $decoded->name;
        }
        
        if (!empty($decoded->given_name) && !empty($decoded->family_name)) {
            return trim($decoded->given_name . ' ' . $decoded->family_name);
        }
        
        if (!empty($decoded->given_name)) {
            return $decoded->given_name;
        }
        
        if (!empty($decoded->preferred_username)) {
            return $decoded->preferred_username;
        }
        
        if (!empty($decoded->email)) {
            return explode('@', $decoded->email)[0]; // Use email prefix as fallback
        }
        
        return 'Keycloak User'; // Ultimate fallback
    }

    /**
     * Extract email verification status from token
     */
    protected function extractEmailVerification($decoded)
    {
        if (isset($decoded->email_verified) && $decoded->email_verified === true) {
            return now();
        }
        
        return null;
    }

    public function check()
    {
        return $this->user() !== null;
    }

    public function guest()
    {
        return !$this->check();
    }

    public function id()
    {
        return $this->user() ? $this->user()->getAuthIdentifier() : null;
    }

    public function validate(array $credentials = [])
    {
        return false;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function hasUser()
    {
        return $this->user !== null;
    }
}