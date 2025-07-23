<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Auth\CustomUserProvider;

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
            return null;
        }

        $tokenString = $matches[1];

        // ✅ Replace this with your actual public key
        $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwFXQ8TQN9VRUFARO0wMT
EKaflXfPXoDvF3Ona5CSj+Cv2d/Z51XouZcMf1VoKySAS3sf9DhWkjPzo4ydOmjs
HpqmToFnNwFshB9CtspwmHVESDyxKD08JOEW6wMJBi6qAGiY393BZPXUmfpnRURQ
67MVlY3sf2yzbcoU+7tiL5VXM+kZjcRLZlSuciB7flgf6icYU7QvMg5yyGOQ3Mb8
RU6tToVlpY+vrUgyWXYBLRDW4qbXZRv9VyqMozfrBrn8jADhNbcZykPE2vEg1Ch9
wvTl93QIhW/JK2GmJTmJAeuO131I5TPC+d+OXJgJUFoVurJn1Fpf4rWDggIHSqU5
pwIDAQAB
-----END PUBLIC KEY-----
EOD;

        try {
            $decoded = JWT::decode($tokenString, new Key($publicKey, 'RS256'));

            $keycloakId = $decoded->sub ?? null;
            if (!$keycloakId) {
                return null;
            }

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

            if (!$user) {
                return null;
            }

            $this->user = $user;
            return $this->user;
        } catch (\Exception $e) {
            return null;
        }
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
