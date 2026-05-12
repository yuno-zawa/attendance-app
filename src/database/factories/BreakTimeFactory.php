<?php

namespace Database\Factories;

use App\Models\BreakTime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BreakTime>
 */
class BreakTimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'break_in' => $this->faker->time('H:i'),
            'break_out' => $this->faker->time('H:i'),
        ];
    }
}
