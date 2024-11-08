<?php

namespace Database\Factories;

use App\Models\Vehicle; // Ensure you import the Vehicle model
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'vehicle_type_id' => \App\Models\VehicleType::factory(),
            'model' => $this->faker->word(),
            'make' => $this->faker->company(),
            'year' => $this->faker->year(),
            'license_plate' => strtoupper($this->faker->bothify('???-####')),
            'color' => $this->faker->safeColorName(),
            'mileage' => $this->faker->numberBetween(5000, 200000),
        ];
    }
}
