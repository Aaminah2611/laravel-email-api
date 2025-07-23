<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'keycloak_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function findByKeycloakIdOrEmail($value)
    {
        return self::where('keycloak_id', $value)
                   ->orWhere('email', $value)
                   ->first();
    }

    // Add this method:
    public static function getByKeycloakId(string $keycloakId)
    {
        return self::where('keycloak_id', $keycloakId)->first();
    }
}

