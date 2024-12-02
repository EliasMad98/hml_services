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
    public function definition()
    {
        return [
            'first_name' => $this->faker->name(),
            'last_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'email_verified_at' => now(),
            'password' =>' eyJpdiI6Ikh6bGNPbDZ2UndJSVVqbzd2c2tobEE9PSIsInZhbHVlIjoiclZqdllWM0dPWDFBcThucUZVWGplZz09IiwibWFjIjoiMDQ5ODA4NzY2ZmU5YjNlMmFjYTg3NjBlYTIyMzJmMmE2NmRjMzljNzY3NjgxZjA4N2I0YzIxZmY3ZWViOWFlMyIsInRhZyI6IiJ9', // 09926158771977
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
