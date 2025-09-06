<?php

namespace App\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
        
        // Register custom Passport guard
        $this->app['auth']->extend('custom_passport', function ($app, $name, array $config) {
            return new \App\Guards\CustomPassportGuard(
                $app['auth']->createUserProvider($config['provider']),
                $app['request']
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceScheme('https');
        $rootUrl = request()->getSchemeAndHttpHost() . '/app/api';
        
        URL::forceRootUrl($rootUrl);
        JsonResource::withoutWrapping();
        
        // CRITICAL: Configure Passport early and ensure it's properly loaded
        $this->configurePassport();
    }

    /**
     * Configure Passport with proper settings
     */
    private function configurePassport(): void
    {
        // Set token expiration times
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        
        // Enable password grant
        Passport::enablePasswordGrant();
        
        // Ensure proper key paths
        Passport::loadKeysFrom(storage_path());
        
        // Log configuration for debugging
        if (app()->environment('production')) {
            \Log::info('Passport configured', [
                'keys_path' => storage_path(),
                'private_key_exists' => file_exists(storage_path('oauth-private.key')),
                'public_key_exists' => file_exists(storage_path('oauth-public.key'))
            ]);
        }
    }
}
