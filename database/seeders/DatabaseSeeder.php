<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            [
                'country_code' => '+91',
                'mobile_number' => '9876543210',
            ],
            [
                'name' => 'Test User',
                'date_of_birth' => '1996-05-28',
                'sex' => 'Male',
            ],
        );
    }
}
