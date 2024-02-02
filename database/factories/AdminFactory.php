<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email'=> $this->faker->safeEmail,
            'phone_number' => $this->faker->unique()->numberBetween(9800000000, 9899999999),
            'status' => rand(0, 1),
            'role' => $this->faker->randomElement([2, 1]), // Randomly set the role to 0 or 1
            'password' => 'password', // You might want to use a more secure password generation method
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'phone_verified_at' => null,
        ]);
    }
}
