<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Professional;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'role' => 'user',
            'phone' => '081234567890',
        ]);

        $professionals = User::factory(10)->professional()->create()->each(function ($user) {
            $professional = Professional::factory()->create([
                'user_id' => $user->id,
            ]);

            Schedule::factory(10)->create([
                'professional_id' => $professional->id,
            ]);
        });

        $regularUsers = User::factory(5)->create();

        foreach ($regularUsers as $user) {
            $professional = Professional::inRandomOrder()->first();
            $schedule = $professional->schedules()->inRandomOrder()->first();

            if ($schedule) {
                Appointment::factory()->create([
                    'user_id' => $user->id,
                    'professional_id' => $professional->id,
                    'schedule_id' => $schedule->id,
                    'appointment_date' => $schedule->date,
                    'start_time' => $schedule->start_time,
                    'price' => $professional->rate_30min,
                ]);
            }
        }

        // Seed Articles
        $this->call(ArticleSeeder::class);
    }
}
