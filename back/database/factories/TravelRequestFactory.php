<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TravelRequest>
 */
class TravelRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'destination' => $this->faker->city(),
            'departure_date' => $this->faker->dateTimeBetween('now', '+1 week'),
            'return_date' => $this->faker->dateTimeBetween('+1 week', '+2 weeks'),
            'status' => 'requested',
        ];
    }
}