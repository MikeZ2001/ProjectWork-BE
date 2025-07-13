<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\DB;

abstract class WithDatabaseSetupTest extends TestCase
{
    public function setup(): void
    {
        parent::setUp();
        $this->dbName = 'test_db' . rand(1, 10000);
        $this->createSchemaAndUser($this->dbName);
        DB::statement('USE ' . $this->dbName);
    }

    /**
     * Create a new database and a related user for a given actor.
     *
     * @param string $name
     */
    protected function createSchemaAndUser(string $name): void
    {
        DB::statement("CREATE DATABASE IF NOT EXISTS $name");
        DB::statement("CREATE USER IF NOT EXISTS '$name'@'%' IDENTIFIED BY '$name'");
        DB::statement("GRANT ALL PRIVILEGES ON $name.* TO '$name'@'%';");
        // To run triggers.
        DB::statement("GRANT SUPER ON *.* TO '$name'@'%'");
        DB::statement("GRANT SYSTEM_USER ON *.* TO '$name'@'%'");
    }

    /**
     * Drop database and related user for a given actor.
     *
     * @param string $name
     */
    protected function dropSchemaAndUser(string $name): void
    {
        DB::statement("DROP DATABASE IF EXISTS $name");
        DB::statement("DROP USER IF EXISTS '$name'@'%'");
    }

    protected function tearDown(): void
    {
        $this->dropSchemaAndUser($this->dbName);
        parent::tearDown();
    }
}
