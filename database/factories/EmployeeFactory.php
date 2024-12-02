<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
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
            'job_type_id' => $this->faker->numberBetween(1, 4),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'password' =>'eyJpdiI6ImdMQzBPK2ZsRkRGUXpYUkVJMGF1Rmc9PSIsInZhbHVlIjoieUJ2NmkySXg4c2c1TFFjWFBnNGpsdz09IiwibWFjIjoiNzk0YTA5NjRlZTZhZTZiODk3ZjU2YmQzYWRlMDJkNTI0MjEzNGYyMDU3YTZlYWVkNmFkZWQ2NTkyMTUwZDA0ZCIsInRhZyI6IiJ9', // password
        ];
    }
}
