<?php

namespace Modules\OAuth\Providers;


use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Nwidart\Modules\Traits\PathNamespace;


class OAuthServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'OAuth';

    protected string $nameLower = 'oauth';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
        
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        Passport::enablePasswordGrant();
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }
    
    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
