<?php

namespace Modules\OAuth\Providers;


use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Nwidart\Modules\Traits\PathNamespace;
use Modules\OAuth\Console\Commands\EnsurePasswordClient;

class OAuthServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $moduleName = 'OAuth';

    protected string $moduleNameLower = 'oauth';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
        
        // Only configure Passport if not already configured in main AppServiceProvider
        if (!config('passport.tokens_expire_in')) {
            Passport::tokensExpireIn(now()->addDays(15));
            Passport::refreshTokensExpireIn(now()->addDays(30));
            Passport::personalAccessTokensExpireIn(now()->addMonths(6));
            Passport::enablePasswordGrant();
        }
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
        $this->commands([
            EnsurePasswordClient::class,
        ]);
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
