<?php

namespace Modules\Account\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Food & Dining',
            'Utilities',
            'Rent / Mortgage',
            'Transportation',
            'Health & Fitness',
            'Entertainment',
            'Education',
            'Shopping',
            'Travel',
            'Salary',
            'Investment Income',
            'Gifts & Donations'
        ];

        $insertData = array_map(function($name) {
            return [
                'name'       => $name,
                'created_at' => now(),
            ];
        }, $categories);

        DB::table('categories')->insert($insertData);
    }
}