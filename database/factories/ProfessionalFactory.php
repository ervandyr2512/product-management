<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Professional>
 */
class ProfessionalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(['psychiatrist', 'psychologist', 'conversationalist']);

        return [
            'type' => $type,
            'license_number' => $type !== 'conversationalist' ? fake()->bothify('LIC-####-????') : null,
            'bio' => fake()->paragraph(3),
            'specialization' => fake()->randomElement([
                'Anxiety Disorders',
                'Depression',
                'Stress Management',
                'Relationship Counseling',
                'Career Counseling',
                'Family Therapy',
                'Trauma and PTSD',
                'Addiction Recovery'
            ]),
            'experience_years' => fake()->numberBetween(1, 20),
            'rate_30min' => fake()->randomFloat(2, 100000, 300000),
            'rate_60min' => fake()->randomFloat(2, 150000, 500000),
            'profile_photo' => null,
            'is_active' => true,
        ];
    }
}
