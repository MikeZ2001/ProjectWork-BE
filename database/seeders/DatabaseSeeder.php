<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Account\Database\Seeders\CategoriesSeeder;
use Modules\User\Database\Seeders\UsersTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            CategoriesSeeder::class,
        ]);
    }
}
