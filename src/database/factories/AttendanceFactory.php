<?php

namespace Database\Factories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = $this->faker->dateTimeBetween('-1 month', 'now');
        $checkOut = (clone $checkIn)->modify('+8 hours');
        return [
            'user_id' => User::factory(),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
        ];
    }
}
