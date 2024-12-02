<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            //
            'non_tenant_id' => $this->faker->numberBetween(1, 50),
            'location_details' => $this->faker->randomElement(['شارع 1', 'شارع 2', 'شارع 3', 'شارع 4', 'شارع 5','شارع 6']),
            'location_name' => $this->faker->randomElement(['location']),
            'unit_number' => $this->faker->numberBetween(1, 20),
            'unit_type' => $this->faker->randomElement(['شارع 1', 'شارع 2', 'شارع 3', 'شارع 4', 'شارع 5','شارع 6']),
            'contact_name' => $this->faker->name(),
            'contact_mobile' => $this->faker->phoneNumber(),
            'lat' => $this->faker->longitude($min = 47, $max = 49),
            'long' => $this->faker->latitude($min = 28, $max = 30),

        ];
    }
}
