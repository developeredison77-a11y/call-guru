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
            'mobile_number' => (string) fake()->unique()->numberBetween(6000000000, 9999999999),
            'age' => fake()->numberBetween(18, 60),
            'sex' => fake()->randomElement(['Male', 'Female', 'Other']),
            'remember_token' => Str::random(10),
        ];
    }
}
