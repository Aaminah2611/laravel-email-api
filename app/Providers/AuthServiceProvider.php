<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Auth\CustomUserProvider;
use App\Auth\KeycloakGuard;
use Illuminate\Support\Facades\Log;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        Log::info('AuthServiceProvider booting, registering custom user provider');
        
        // Register the keycloak user provider (this should match config/auth.php)
        Auth::provider('keycloak', function ($app, array $config) {
            return new CustomUserProvider($app['hash'], $config['model']);
        });
        
        // Register the keycloak guard driver
        Auth::extend('keycloak', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);
            if (!$provider) {
                Log::error("User provider [{$config['provider']}] is not defined.");
                throw new \Exception("User provider [{$config['provider']}] is not defined.");
            }
            
            Log::info('KeycloakGuard being instantiated', [
                'provider_class' => get_class($provider),
                'config' => $config
            ]);
            
            return new KeycloakGuard($provider, $app['request']);
        });
    }
}