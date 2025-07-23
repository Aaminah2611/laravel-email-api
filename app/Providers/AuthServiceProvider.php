<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider; // policies support
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

        // Register the custom user provider driver
        Auth::provider('custom_user_provider', function ($app, array $config) {
            return new CustomUserProvider($app['hash'], $config['model']);
        });

        // Register the keycloak guard driver
        Auth::extend('keycloak', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);

            if (!$provider) {
                Log::error("User provider [{$config['provider']}] is not defined.");
                throw new \Exception("User provider [{$config['provider']}] is not defined.");
            }

            return new KeycloakGuard($provider, $app['request']);
        });
    }
}
