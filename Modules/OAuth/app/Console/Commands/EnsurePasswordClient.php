<?php

namespace Modules\OAuth\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Client;

class EnsurePasswordClient extends Command
{
    protected $signature = 'oauth:ensure-password-client {--name=ProjectWork Password Grant Client}';

    protected $description = 'Ensure a Passport password grant client exists; create one if missing and print credentials.';

    public function handle(): int
    {
        $existing = Client::query()
            ->where('password_client', 1)
            ->where('revoked', 0)
            ->first();

        if ($existing) {
            $this->info('Password grant client already exists.');
            $this->line('Client ID: '.$existing->id);
            return self::SUCCESS;
        }

        $this->info('No password grant client found. Creating one...');

        // Run Passport command non-interactively and capture output
        $buffer = '';
        Artisan::call('passport:client', [
            '--password' => true,
            '--name' => $this->option('name'),
            '--no-interaction' => true,
        ]);
        $buffer = Artisan::output();

        // Try to extract Client ID and Secret from output
        $clientId = null;
        $clientSecret = null;
        foreach (explode("\n", $buffer) as $line) {
            if (stripos($line, 'Client ID') !== false) {
                $clientId = trim(preg_replace('/^.*?:\s*/', '', $line));
            }
            if (stripos($line, 'Client secret') !== false) {
                $clientSecret = trim(preg_replace('/^.*?:\s*/', '', $line));
            }
        }

        // Fallback: fetch the newly created client if ID was not parsed
        if (!$clientId) {
            $latest = Client::query()->where('password_client', 1)->orderByDesc('id')->first();
            if ($latest) {
                $clientId = (string) $latest->id;
            }
        }

        $this->line('Done.');
        if ($clientId) {
            $this->line('Client ID: '.$clientId);
        }
        if ($clientSecret) {
            $this->line('Client Secret: '.$clientSecret);
        } else {
            $this->warn('Client Secret not shown. If secrets are hashed, retrieve it from the command output above.');
        }

        return self::SUCCESS;
    }
} 