<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Building>
 */
class BuildingFactory extends Factory
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
            'location_details' => 'دبي',
            'street_name' => $this->faker->randomElement(['شارع 1', 'شارع 2', 'شارع 3', 'شارع 4', 'شارع 5','شارع 6']),
            'building_name' => $this->faker->randomElement(['برج خليفة', 'برج العرب', 'برج 3', 'برج 2', 'برج 1','برج 4']),
            'building_number' => $this->faker->randomElement(['بلوك 5', 'بلوك 4', 'بلوك 3', 'بلوك 2', 'بلوك 1','بلوك 6', 'بلوك 7', 'بلوك 8', 'بلوك 9', 'بلوك 10', 'بلوك 11', 'بلوك 12']),
            'lat' => $this->faker->longitude($min = 47, $max = 49),
            'long' => $this->faker->latitude($min = 28, $max = 30),

        ];
    }
}
