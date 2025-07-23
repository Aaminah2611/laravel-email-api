<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use App\Models\User;

class CustomUserProvider extends EloquentUserProvider
{
    public function findByKeycloakIdOrEmail(string $keycloakId, ?string $email = null): ?User
    {
        $query = User::where('keycloak_id', $keycloakId);
        
        if ($email) {
            $query->orWhere('email', $email);
        }
        
        return $query->first();
    }
}
