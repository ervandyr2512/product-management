<?php

namespace Database\Seeders;

use App\Models\Professional;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all professionals
        $professionals = Professional::all();

        if ($professionals->isEmpty()) {
            $this->command->info('No professionals found. Please run ProfessionalSeeder first.');
            return;
        }

        $this->command->info('Creating schedules for professionals...');

        foreach ($professionals as $professional) {
            // Create schedules for the next 7 days
            for ($day = 0; $day < 7; $day++) {
                $date = Carbon::now()->addDays($day)->format('Y-m-d');

                // Morning sessions (09:00 - 12:00)
                $morningSlots = [
                    ['09:00', '10:00'],
                    ['10:00', '11:00'],
                    ['11:00', '12:00'],
                ];

                // Afternoon sessions (13:00 - 17:00)
                $afternoonSlots = [
                    ['13:00', '14:00'],
                    ['14:00', '15:00'],
                    ['15:00', '16:00'],
                    ['16:00', '17:00'],
                ];

                // Evening sessions (18:00 - 21:00)
                $eveningSlots = [
                    ['18:00', '19:00'],
                    ['19:00', '20:00'],
                    ['20:00', '21:00'],
                ];

                // Combine all slots
                $allSlots = array_merge($morningSlots, $afternoonSlots, $eveningSlots);

                // Randomly select 5-8 slots per day for variety
                $numSlots = rand(5, 8);
                $selectedSlots = array_rand($allSlots, $numSlots);

                if (!is_array($selectedSlots)) {
                    $selectedSlots = [$selectedSlots];
                }

                foreach ($selectedSlots as $slotIndex) {
                    $slot = $allSlots[$slotIndex];

                    Schedule::create([
                        'professional_id' => $professional->id,
                        'date' => $date,
                        'start_time' => $slot[0],
                        'end_time' => $slot[1],
                        'is_available' => true,
                    ]);
                }
            }

            $this->command->info("Created schedules for {$professional->user->name}");
        }

        $this->command->info('Schedules created successfully!');
    }
}
