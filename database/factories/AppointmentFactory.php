<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $duration = fake()->randomElement(['30', '60']);
        $appointmentDate = fake()->dateTimeBetween('now', '+30 days');
        $startHour = fake()->numberBetween(8, 16);
        $startTime = sprintf('%02d:00:00', $startHour);
        $endTime = $duration === '30'
            ? sprintf('%02d:30:00', $startHour)
            : sprintf('%02d:00:00', $startHour + 1);

        return [
            'appointment_date' => $appointmentDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration' => $duration,
            'price' => $duration === '30' ? fake()->randomFloat(2, 100000, 300000) : fake()->randomFloat(2, 150000, 500000),
            'status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
            'video_chat_link' => fake()->url(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
