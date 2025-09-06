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
        
        // Passport is configured in the OAuth module
    }
}
