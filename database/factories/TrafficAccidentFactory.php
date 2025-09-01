<?php

namespace Database\Factories;

use App\Models\TrafficAccident;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrafficAccidentFactory extends Factory
{
    protected $model = TrafficAccident::class;

    public function definition(): array
    {
        return [
            'city' => $this->faker->city,
            'district' => $this->faker->streetName,
            'vehicles_involved' => rand(1, 10),
            'victims_total' => rand(1, 20),
            'victims_md' => rand(0, 5),
            'victims_ll' => rand(0, 15),
            'documents' => rand(1, 20),
            'compensation' => $this->faker->numberBetween(1000000, 100000000),
            'action_plan' => json_encode(['Rambu tambahan', 'Penerangan jalan']),
            'latitude' => $this->faker->latitude(-6, -7),
            'longitude' => $this->faker->longitude(106, 108),
        ];
    }
}
