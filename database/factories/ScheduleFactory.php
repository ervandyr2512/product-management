<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $counter = 0;
        $counter++;

        $daysAhead = floor($counter / 8);
        $hourSlot = ($counter % 8) + 8;

        $date = now()->addDays($daysAhead)->format('Y-m-d');
        $startTime = sprintf('%02d:00:00', $hourSlot);
        $endTime = sprintf('%02d:00:00', $hourSlot + 1);

        return [
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'is_available' => true,
        ];
    }
}
