<?php

namespace Tests;

class WithMigrationTest extends WithDatabaseSetupTest
{
    public function setup(): void
    {
        parent::setup();
        $command = $this->artisan('migrate');
        $command->assertSuccessful()->run();
    }
}