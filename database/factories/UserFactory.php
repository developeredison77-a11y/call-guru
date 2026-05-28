<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'country_code' => '+91',
            'mobile_number' => (string) fake()->unique()->numberBetween(6000000000, 9999999999),
            'date_of_birth' => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'sex' => fake()->randomElement(['Male', 'Female', 'Other']),
            'remember_token' => Str::random(10),
        ];
    }
}
