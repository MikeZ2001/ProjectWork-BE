<?php

namespace Tests;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\PendingCommand;
use Laravel\Passport\Passport;
use Modules\Account\Database\Factories\AccountFeatureTestFactory;

use Modules\User\Models\User;

class WithUserTest extends WithMigrationTest
{
    /**
     * @var string
     */
    protected string $password = 'password';

    /**
     * @var string
     */
    protected string $token;

    /**
     * @var User
     */
    protected User $user;

    protected string $dbName;


    /**
     * Setup the test environment.
     *
     */
    public function setup(): void
    {
        parent::setup();
        $appName = Config::get('app.name');
        Passport::$hashesClientSecrets = false;
        /** @var PendingCommand $command */
        $command = $this->artisan(
            'passport:client',
            ['--name' => $appName, '--personal' => null]
        );
        $command->assertSuccessful()->run();
        $this->user = $this->makeTestAuthUser();
        $this->token = $this->user->createToken('UnitTest')->accessToken;
        $this->actingAs($this->user);
    }

    /**
     * Clean up the testing environment before the next test.
     *
     */
    public function tearDown(): void
    {
        $this->user->delete();
        parent::tearDown();
    }

    /**
     * Create a new test user using the UserFactory
     *
     * @return User
     */
    private function makeTestAuthUser(): User
    {
        return User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
