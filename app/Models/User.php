<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'keycloak_id',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Laravel expects this method when authenticating.
     * Returns password (null allowed for Keycloak users).
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthIdentifierName()
    {
        return $this->getKeyName();
    }

    /**
     * Find a user by Keycloak ID or email.
     */
    public static function findByKeycloakIdOrEmail($value)
    {
        return self::where('keycloak_id', $value)
                   ->orWhere('email', $value)
                   ->first();
    }

    /**
     * Get user by Keycloak ID only.
     */
    public static function getByKeycloakId(string $keycloakId)
    {
        return self::where('keycloak_id', $keycloakId)->first();
    }
}
