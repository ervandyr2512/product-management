<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cart>
 */
class CartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $duration = fake()->randomElement(['30', '60']);

        return [
            'duration' => $duration,
            'price' => $duration === '30' ? fake()->randomFloat(2, 100000, 300000) : fake()->randomFloat(2, 150000, 500000),
        ];
    }
}
