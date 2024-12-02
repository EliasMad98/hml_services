<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Apartment>
 */
class ApartmentFactory extends Factory
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
            'building_id' =>  $this->faker->numberBetween(1, 100),
            'tenant_id' => $this->faker->numberBetween(1, 50),
            'location_name' => $this->faker->randomElement([' location', ' location', 'location ']),
            'unit_number' => $this->faker->numberBetween(1, 50),
            'unit_type' => $this->faker->randomElement([' سكني', ' تجاري', ' استوديو']),

        ];
    }
}
